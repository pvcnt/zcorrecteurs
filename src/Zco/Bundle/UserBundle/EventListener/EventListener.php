<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\UserBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Zco\Bundle\UserBundle\Event\CheckValueEvent;
use Zco\Bundle\UserBundle\Exception\LoginException;
use Zco\Bundle\UserBundle\UserEvents;

/**
 * Observateur chargé des événements du kernel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 127),
            KernelEvents::CONTROLLER => 'onKernelController',
            UserEvents::VALIDATE_EMAIL => 'onValidateEmail',
            PagesEvents::SITEMAP => 'onFilterSitemap',
            CoreEvents::DAILY_CRON => 'onDailyCron',
        );
    }

    /**
     * Met à jour les permissions en temps réel si cela a été demandé,
     * s'occupe de la connexion automatique avec les cookies
     * ainsi que de la vérification du bannissement d'un membre.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        session_start();

        //Définit certaines variables importantes de session si ce n'est pas
        //encore le cas.
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = md5(uniqid(rand(), true));
        }
        if (!isset($_SESSION['erreur'])) {
            $_SESSION['erreur'] = array();
        }
        if (!isset($_SESSION['message'])) {
            $_SESSION['message'] = array();
        }

        $user = $this->container->get('zco_user.user');

        //Mise à jour temps réel des groupes associés au compte de
        //l'utilisateur actuellement connecté.
        if (
            $user->isAuthenticated()
            && isset($_SESSION['refresh_droits'])
            && $this->container->get('zco_core.cache')->get('dernier_refresh_droits') >= $_SESSION['refresh_droits']
        ) {
            $user->reloadGroups();
        }

        //Tentative de connexion depuis l'environnement courant. Normalement seul
        //->login() peut générer une LoginException, mais on préfère encadrer le
        //tout par un try{ } en cas d'observateur mal écrit.
        try {
            if (($userEntity = $user->attemptEnvLogin($event->getRequest())) instanceof \Utilisateur) {
                $user->login($userEntity);
            }
        } catch (LoginException $e) {
            //Ne rien faire, la connexion par l'environnement a simplement échoué.
        }

        //Si le membre n'est toujours pas connecté on lui attribue de force
        //les attributs habituellement liés au compte.
        if (!isset($_SESSION['groupe']) || !isset($_SESSION['id'])) {
            $_SESSION['groupe'] = InfosGroupe(\Groupe::ANONYMOUS)['groupe_id'];
            $_SESSION['id'] = -1;
            $_SESSION['refresh_droits'] = time();
        }

        //Permet de stocker dans les logs Apache le pseudo de chaque membre.
        //On pose un cookie car on ne peut que récupérer un cookie avec Apache.
        if (isset($_SESSION['pseudo']) AND !isset($_COOKIE['pseudo'])) {
            setcookie('pseudo', $_SESSION['pseudo'], strtotime('+1 day'), '/');
        }

        // Check for IP ban.
        $cache = $this->container->get('zco_core.cache');
        $ip = ip2long($this->container->get('request')->getClientIp(true));
        $ips = $cache->get('ips_bannies');
        if ($ips === false) {
            $ips = array();
            $dbh = \Doctrine_Manager::connection()->getDbh();
            $stmt = $dbh->prepare("SELECT ip_ip FROM zcov2_ips_bannies WHERE ip_fini = 0");
            $stmt->execute();
            $retour = $stmt->fetchAll();
            $stmt->closeCursor();
            if (!empty($retour)) {
                foreach ($retour as $cle => $valeur) {
                    $ips[] = $valeur['ip_ip'];
                }
            }
            $cache->set('ips_bannies', $ips, 0);
        }

        if (in_array($ip, $ips)) {
            // We fetch reason and duration of ban.
            $dbh = \Doctrine_Manager::connection()->getDbh();
            $stmt = $dbh->prepare("SELECT ip_raison, ip_duree_restante, ip_date " .
                "FROM zcov2_ips_bannies " .
                "WHERE ip_ip = :ip");
            $stmt->bindParam(':ip', $ip);
            $stmt->execute();
            $retour = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!empty($retour)) {
                $Raison = $retour['ip_raison'];
                $Duree = $retour['ip_duree_restante'];
                $Debut = $retour['ip_date'];
                $_SESSION = array();
                session_destroy();
                $event->setResponse(new Response(render_to_string('ZcoUserBundle::banni.html.php', compact('Debut', 'Duree', 'Raison'))));
            }
        }
    }

    /**
     * Bannit certaines adresses courriel interdites via l'interface graphique
     * correspondante.
     *
     * @param CheckValueEvent $event
     */
    public function onValidateEmail(CheckValueEvent $event)
    {
        if (\Doctrine_Core::getTable('BannedEmail')->isBanned($event->getValue())) {
            $event->reject('Cette adresse courriel n\'est pas autorisée.');
        }
    }

    /**
     * Met à jour la position courante du visiteur sur le site (a besoin
     * pour cela que le contrôleur ait déjà été choisi) ainsi que sa
     * dernière adresse IP.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $this->container->get('request');

        if (
            //Requête asynchrone type Ajax
            !$request->isXmlHttpRequest()
            //Chargement d'une page non HMTL (flux, Javascript, etc.)
            && $request->attributes->get('_format', 'html') === 'html'
            //Route interne (type profiler)
            && substr($request->attributes->get('_route'), 0, 1) !== '_'
        ) {
            $this->refreshSession();
        }
    }

    /**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
    public function onFilterSitemap(FilterSitemapEvent $event)
    {
        $router = $this->container->get('router');
        $event->addLink($router->generate('zco_user_session_register', array(), true), array(
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ));
        $event->addLink($router->generate('zco_user_session_login', array(), true), array(
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ));
        $event->addLink($router->generate('zco_user_index', array(), true), array(
            'changefreq' => 'daily',
            'priority' => '0.5',
        ));
    }

    /**
     * Actions à exécuter quotidiennement.
     *
     * @param CronEvent $event
     */
    public function onDailyCron(CronEvent $event)
    {
        //Supprime les sauvegardes de zForm vieilles de plus d'un jour.
        \Doctrine_Core::getTable('ZformBackup')->purge();

        //Supprime les comptes non-validés de plus d'un jour.
        \Doctrine_Core::getTable('Utilisateur')->purge();

        //Mise à jour des sanctions. Pour cette action on ne DOIT jamais avoir
        //la possibilité de l'exécuter plus d'une fois par jour, sans quoi les
        //sanctions dureront moins de temps que prévu.
        if ($event->ensureDaily()) {
            \Doctrine_Core::getTable('UserPunishment')->purge();
        }

        //Suppression de l'historique des adresses IP de plus d'un an.
        //Ne surtout pas supprimer (déclaration CNIL, toussa).
        \Doctrine_Core::getTable('UtilisateurIp')->purge();

        //Mise à jour des bannissements d'adresses IP.
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_duree_restante = ip_duree_restante - 1
		WHERE ip_fini = 0 AND ip_duree_restante > 0 AND (ip_date + INTERVAL (ip_duree - ip_duree_restante) DAY < NOW())");
        $stmt->execute();
        $stmt->closeCursor();

        $stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_fini = 1
		WHERE ip_duree > 0 AND ip_duree_restante = 0");
        $stmt->execute();
        $stmt->closeCursor();

        $this->container->get('zco_core.cache')->delete('ips_bannies');
    }

    /**
     * Met à jour les différentes données liées à la session, en particulier la
     * table gardant une trace des visiteurs navigant actuellement sur le site.
     */
    private function refreshSession()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $request = $this->container->get('request');
        $ip = ip2long($request->getClientIp(true));

        //Si la dernière IP diffère, on la met à jour (en cas de membre connecté uniquement)
        if ((!isset($_SESSION['last_ip']) || $_SESSION['last_ip'] != $ip) && verifier('connecte')) {
            // Géolocalisation.
            $location = $this->container->get('zco_user.manager.ip')->Geolocaliser($ip);
            $countryName = $location['country'] ?? 'Inconnu';

            // Mise à jour de la table des membres.
            $stmt = $dbh->prepare("UPDATE zcov2_utilisateurs " .
                "SET utilisateur_date_derniere_visite = NOW(), utilisateur_ip = :ip, utilisateur_localisation = :pays " .
                "WHERE utilisateur_id = :id");
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindValue(':pays', $countryName);
            $stmt->execute();
            $stmt->closeCursor();

            // Insertion de la nouvelle ip (ou mise à jour de sa date d'utilisation).
            $proxy = ip2long($request->getClientIp(false));
            $proxy = $proxy === $ip ? null : $proxy;
            $stmt = $dbh->prepare("
				INSERT INTO zcov2_utilisateurs_ips(ip_id_utilisateur, ip_ip, ip_proxy, ip_date_debut, ip_date_last, ip_localisation)
				VALUES(:id, :ip, :proxy, NOW(), NOW(), :pays)
				ON DUPLICATE KEY UPDATE ip_date_last = NOW()");
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':proxy', $proxy);
            $stmt->bindParam(':pays', $countryName);
            $stmt->execute();
            $stmt->closeCursor();

            $_SESSION['last_ip'] = $ip;
        }
    }
}