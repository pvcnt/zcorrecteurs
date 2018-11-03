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

namespace Zco\Bundle\ForumBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\TemplatingEvents;

/**
 * Observateur principal pour le module du forum.
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
            TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
        );
    }

    /**
     * Ajoute la feuille de style CSS du forum sur toutes les actions du module.
     *
     * @param FilterResourcesEvent $event
     */
    public function onTemplatingFilterResources(FilterResourcesEvent $event)
    {
        $request = \Container::request();
        if (
            $request->attributes->has('_module') &&
            $request->attributes->get('_module') === 'forum'
        ) {
            $event->requireResource('@ZcoForumBundle/Resources/public/css/forum.css');
        }
    }
}