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

namespace Zco\Bundle\DonsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;

class EventListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}

	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		$event
		    ->getRoot()
		    ->getChild('Communauté')
		    ->addChild('Faire un don', array(
			    'uri'    => '/dons/',
			    'weight' => 20,
			    'linkAttributes' => array(
				    'rel'   => 'Vous souhaitez aider financièrement le site ? Faites un don !', 
				    'title' => 'Faire un don',
			    )
		    ));
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/dons/', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.5',
		));
		$event->addLink(URL_SITE.'/dons/cheque-ou-virement.html', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.3',
		));
		$event->addLink(URL_SITE.'/dons/deduction-fiscale.html', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.3',
		));
	}
}