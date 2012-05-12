<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\LivredorBundle\EventListener;

use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Observateur principal pour le livre d'or.
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
			'zco_core.filter_menu.footer1' => 'onFilterFooter1',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Ajoute un lien vers le livre d'or dans le peid de page.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterFooter1(FilterMenuEvent $event)
	{
		if (!verifier('livredor'))
		{
		    return;
	    }
	    
		$event->getRoot()->addChild('Livre d\'or', array(
			'uri'    => '/livredor/',
			'weight' => 40,
			'linkAttributes' => array(
				'title' => 'Vous aimez ce site ? N\'hésitez pas à laisser un message sur le livre d\'or.'
			),
		));
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/livredor/', array(
			'changefreq' => 'weekly',
			'priority'	 => '0.3',
		));
	}
}