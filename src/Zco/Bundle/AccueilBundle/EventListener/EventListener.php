<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\AccueilBundle\EventListener;

use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Observateur principal pour le module d'accueil.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
			'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
			InformationsEvents::SITEMAP       => 'onFilterSitemap',
        );
    }
    
    /**
     * Ajoute un lien vers l'accueil dans la barre de navigation.
     *
     * @param FilterMenuEvent $event
     */
	public function onFilterSpeedbarre(FilterMenuEvent $event)
	{
		$event
			->getRoot()
			->addChild('Accueil', array('uri' => '/', 'weight' => 0))
			->setCurrent($event->getRequest()->attributes->get('_module') === 'accueil');
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE, array(
			'changefreq' => 'daily',
			'priority'	 => '0.9',
		));
	}
}