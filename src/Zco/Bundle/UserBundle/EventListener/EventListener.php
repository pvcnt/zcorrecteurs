<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\GroupesBundle\Domain\GroupDAO;
use Zco\Bundle\UserBundle\Exception\LoginException;

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
            KernelEvents::REQUEST => ['onKernelRequest', 127],
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

        if (session_status() === PHP_SESSION_NONE) {
            // TODO: investiguer pourquoi kernel.request semble être déclenché deux fois.

            session_start();
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

            // Mise à jour temps réel des groupes associés au compte de
            // l'utilisateur actuellement connecté.
            if ($user->isAuthenticated() && isset($_SESSION['refresh_droits'])) {
                $forceRefresh = $this->container->get('cache')->fetch('dernier_refresh_droits');
                if ($forceRefresh !== false && $forceRefresh >= $_SESSION['refresh_droits']) {
                    $user->reloadGroups();
                }
            }

            // Tentative de connexion depuis l'environnement courant. Normalement seul
            // ->login() peut générer une LoginException, mais on préfère encadrer le
            // tout par un try{ } en cas d'observateur mal écrit.
            try {
                if (($userEntity = $user->attemptEnvLogin($event->getRequest())) instanceof \Utilisateur) {
                    $user->login($event->getRequest(), $userEntity);
                }
            } catch (LoginException $e) {
                // Ne rien faire, la connexion par l'environnement a simplement échoué.
            }

            // Si le membre n'est toujours pas connecté on lui attribue de force certains
            // attributs l'identifiant comme un visiteur.
            if (!isset($_SESSION['groupe']) || !isset($_SESSION['id'])) {
                $_SESSION['groupe'] = GroupDAO::InfosGroupe(\Groupe::ANONYMOUS)['groupe_id'];
                $_SESSION['id'] = -1;
                $_SESSION['refresh_droits'] = time();
            }
        }
    }

    /**
     * Actions à exécuter quotidiennement.
     *
     * @param CronEvent $event
     */
    public function onDailyCron(CronEvent $event)
    {
        //Supprime les comptes non-validés de plus d'un jour.
        \Doctrine_Core::getTable('Utilisateur')->purge();
    }
}