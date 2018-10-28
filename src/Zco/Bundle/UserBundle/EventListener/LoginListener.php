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
use Zco\Bundle\UserBundle\Event\EnvLoginEvent;
use Zco\Bundle\UserBundle\Event\FilterLoginEvent;
use Zco\Bundle\UserBundle\Event\FormLoginEvent;
use Zco\Bundle\UserBundle\Event\LoginEvent;
use Zco\Bundle\UserBundle\User\User;
use Zco\Bundle\UserBundle\UserEvents;

/**
 * Observateur lié intégrations les actions de connexion et déconnexion au
 * reste du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class LoginListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            UserEvents::FORM_LOGIN => 'onFormLogin',
            UserEvents::ENV_LOGIN => 'onEnvLogin',
            UserEvents::PRE_LOGIN => 'onPreLogin',
            UserEvents::POST_LOGIN => 'onPostLogin',
        );
    }

    /**
     * Tente de connecter l'utilisateur grâce aux informations stockées dans
     * ses cookies (présents s'il a choisi la connexion automatique).
     *
     * @param EnvLoginEvent $event
     */
    public function onEnvLogin(EnvLoginEvent $event)
    {
        if ($event->getState() > User::AUTHENTICATED_ANONYMOUSLY) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->cookies->has('user_id') || !$request->cookies->has('violon')) {
            return;
        }

        $user = \Doctrine_Core::getTable('Utilisateur')->getById($event->getRequest()->cookies->get('user_id'));
        if ($user && $request->cookies->get('violon') === $this->generateRememberKey($user)) {
            $event->setUser($user, User::AUTHENTICATED_REMEMBERED);
        }
    }

    /**
     * Tente de connecter l'utilisateur suite à une soumission de formulaire.
     *
     * @param FormLoginEvent $event
     */
    public function onFormLogin(FormLoginEvent $event)
    {
        $data = $event->getData();
        if (($user = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($data['pseudo'])) !== false) {
            $event->setUser($user);
        }
    }

    /**
     * Vérifie que l'utilisateur souhaitant se connecter ne soit pas banni et
     * que son compte ait bien été activé.
     *
     * @param FilterLoginEvent $event
     */
    public function onPreLogin(FilterLoginEvent $event)
    {
        if (!verifier('connexion', 0, $event->getUser()->getGroupId())) {
            $event->abort('Vous êtes banni du site et ne pouvez pas conséquent plus vous connecter à votre compte.');
        }
        if (!$event->getUser()->isAccountValid()) {
            $event->abort('Votre compte est pour l\'instant inactif. Vous avez reçu un courriel comportant un lien de validation du compte.');
        }
    }

    /**
     * @param LoginEvent $event
     */
    public function onPostLogin(LoginEvent $event)
    {
        // Dépose les cookies nécessaires à une future connexion automatique après une connexion réalisée avec succès.
        if ($event->isRemember()) {
            setcookie('violon', $this->generateRememberKey($event->getUser()), strtotime("+1 year"), '/');
            setcookie('user_id', $event->getUser()->getId(), strtotime("+1 year"), '/');
        }

        // Détection de l'adresse IP et du pays du membre.
        $ip = ip2long($event->getRequest()->getClientIp());
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $location = $this->container->get('zco_user.manager.ip')->Geolocaliser($ip);
        $countryName = $location['country'] ?? 'Inconnu';

        $stmt = $dbh->prepare('UPDATE zcov2_utilisateurs 
            SET utilisateur_date_derniere_visite = NOW(), 
                utilisateur_ip = :ip, 
                utilisateur_localisation = :pays 
            WHERE utilisateur_id = :id');
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindValue(':pays', $countryName);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Génère une clé qui sera stockée dans les cookies du visiteur afin de
     * se souvenir de lui lors de sa prochaine visite et prouver son identité.
     *
     * @param  \Utilisateur $user
     * @return string
     */
    private function generateRememberKey(\Utilisateur $user)
    {
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        return sha1($browser . $user->getUsername() . $user->getPassword() . 'ezgnmlwxsainymktiwuv');
    }
}