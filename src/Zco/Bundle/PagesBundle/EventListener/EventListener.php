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

namespace Zco\Bundle\PagesBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;

/**
 * Observateur pour les éléments de l'interface.
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
            'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
            'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
            PagesEvents::SITEMAP => 'onFilterSitemap',
            AdminEvents::MENU => 'onFilterAdmin',
        );
    }

    public function onFilterSpeedbarre(FilterMenuEvent $event)
    {
        $event
            ->getRoot()
            ->addChild('Accueil', array('uri' => '/', 'weight' => 0))
            ->setCurrent($event->getRequest()->attributes->get('_module') === 'accueil');
    }

    public function onFilterAdmin(FilterMenuEvent $event)
    {
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get('router');
        $tab = $event
            ->getRoot()
            ->getChild('Contenu')
            ->getChild('Communication');

        $tab->addChild('Modifier les annonces de la page d\'accueil', array(
            'uri' => $router->generate('zco_home_config'),
        ))->secure('gerer_breve_accueil');
    }

    public function onFilterLeftMenu(FilterMenuEvent $event)
    {
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get('router');
        $event->getRoot()->getChild('Communauté')->addChild('L\'équipe', array(
            'uri' => $router->generate('zco_about_team'),
            'weight' => 30,
            'linkAttributes' => array(
                'rel' => 'Une page spéciale pour présenter ceux qui dépensent tant d\'énergie pour corriger vos documents et faire vivre le site.',
                'title' => 'L\'équipe',
            ),
        ));
        $event->getRoot()->getChild('Communauté')->addChild('Faire un don', array(
            'uri' => $router->generate('zco_donate_index'),
            'weight' => 20,
            'linkAttributes' => array(
                'rel' => 'Vous souhaitez aider financièrement le site ? Faites un don !',
                'title' => 'Faire un don',
            )
        ));
    }

    public function onFilterSitemap(FilterSitemapEvent $event)
    {
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get('router');
        $event->addLink($router->generate('zco_about_index', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));
        $event->addLink($router->generate('zco_about_contact', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));
        $event->addLink($router->generate('zco_about_team', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));
        $event->addLink($router->generate('zco_about_corrigraphie', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));
        $event->addLink($router->generate('zco_about_banners', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));
        $event->addLink($router->generate('zco_about_opensource', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.3',
        ));

        $event->addLink($router->generate('zco_donate_index', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));
        $event->addLink($router->generate('zco_donate_otherWays', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));
        $event->addLink($router->generate('zco_donate_fiscalDeduction', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));

        $event->addLink($router->generate('zco_legal_mentions', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));
        $event->addLink($router->generate('zco_legal_privacy', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));
        $event->addLink($router->generate('zco_legal_rules', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'monthly',
            'priority' => '0.2',
        ));

        $event->addLink($router->generate('zco_home', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'daily',
            'priority' => '0.9',
        ));
    }
}