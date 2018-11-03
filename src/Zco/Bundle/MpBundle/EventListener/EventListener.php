<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

namespace Zco\Bundle\MpBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 100),
        );
    }

    /**
     * Enregistre le compteur de tâches d'administration.
     * Met à jour les compteurs de MP pour le membre connecté.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!verifier('connecte')) {
            return;
        }

        // Mise à jour du nombre de MPs non lus.
        $rafraichir = $this->container->get('cache')->fetch('MPnonLu' . $_SESSION['id']);
        if ($rafraichir) {
            $this->container->get('cache')->delete('MPnonLu' . $_SESSION['id']);
        }
        if ($rafraichir || !isset($_SESSION['MPsnonLus'])) {
            include_once(__DIR__ . '/../modeles/mp_cache.php');
            $_SESSION['MPsnonLus'] = CompteMPnonLu();
        }

        // Mise à jour du nombre de MP total.
        if ($rafraichir || !isset($_SESSION['MPs'])) {
            include_once(__DIR__ . '/../modeles/mp_cache.php');
            $_SESSION['MPs'] = CompteMPTotal();
        }
    }
}