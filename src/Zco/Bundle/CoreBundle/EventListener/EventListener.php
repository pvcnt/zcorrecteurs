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

namespace Zco\Bundle\CoreBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\Templating\TemplatingEvents;

/**
 * Subscriber principal du module central du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    private $maintenance = false;

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
            TemplatingEvents::FILTER_VARIABLES => 'onTemplatingFilterVariables',
        );
    }

    /**
     * Initialise des comportements de base communs à toutes les pages du site.
     *
     * @param FilterResourcesEvent $event
     */
    public function onTemplatingFilterResources(FilterResourcesEvent $event)
    {
        // Exposition des routes pour y avoir accès depuis un code Javascript.
        $event->requireResource('@FOSJsRoutingBundle/Resources/public/js/router.js');

        // Statistiques Google Analytics.
        if ($this->container->getParameter('kernel.environment') === 'prod') {
            $event->initBehavior('google-analytics', array(
                'account' => $this->container->getParameter('analytics_account'),
            ));
        }
    }

    /**
     * Opère à quelques ultimes changements concernant les variables globales
     * avant le rendu de la vue.
     *
     * @param FilterVariablesEvent $event
     */
    public function onTemplatingFilterVariables(FilterVariablesEvent $event)
    {
        // Génération d'un fil d'Ariane par défaut si aucun n'a été créé.
        if (empty(\Page::$fil_ariane) && !empty(\Page::$titre)) {
            fil_ariane(\Page::$titre);
        }

        // Ajout de variables au layout.
        $module = \Container::request()->attributes->get('_module');
        $searchSection = ($module === 'blog') ? 'blog' : 'forum';
        $event->set('searchSection', $searchSection);

        $adminCount = $this->container->get('zco.admin')->count();
        $event->set('adminCount', $adminCount);
    }
}