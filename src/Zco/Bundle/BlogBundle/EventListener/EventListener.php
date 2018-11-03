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

namespace Zco\Bundle\BlogBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\Templating\TemplatingEvents;

/**
 * Observateur principal pour le module du blog.
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
            TemplatingEvents::FILTER_VARIABLES => 'onTemplatingFilterVariables',
        );
    }

    /**
     * Ajoute des variables communes à toutes les pages du module de blog.
     *
     * @param FilterVariablesEvent $event
     */
    public function onTemplatingFilterVariables(FilterVariablesEvent $event)
    {
        if (\Container::request()->attributes->get('_module') !== 'blog') {
            return;
        }

        $event->set('AuteursClass', array(3 => 'gras', 2 => 'normal', 1 => 'italique'));
        $event->set('Etats', array(
            BLOG_BROUILLON => 'Brouillon',
            BLOG_PREPARATION => 'En cours de préparation',
            BLOG_PROPOSE => 'Proposé',
            BLOG_REFUSE => 'Refusé',
            BLOG_VALIDE => 'Validé'
        ));
    }
}