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
use Zco\Bundle\UserBundle\Event\FilterLoginEvent;
use Zco\Bundle\UserBundle\Event\LoginEvent;
use Zco\Bundle\UserBundle\Service\IpAddressLocator;
use Zco\Bundle\UserBundle\User\UserSession;
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
            UserEvents::PRE_LOGIN => 'onPreLogin',
            UserEvents::POST_LOGIN => 'onPostLogin',
        );
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
            setcookie('violon', UserSession::generateRememberKey($event->getUser()), strtotime("+1 year"), '/');
            setcookie('user_id', $event->getUser()->getId(), strtotime("+1 year"), '/');
        }

        // Détection de l'adresse IP et du pays du membre.
        $ip = ip2long($event->getRequest()->getClientIp());
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $location = $this->container->get(IpAddressLocator::class)->locate($ip);
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
}