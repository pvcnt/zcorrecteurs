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

namespace Zco\Bundle\AideBundle\EventListener;

use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterContentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Observateur principal pour le module d'aide.
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
			'zco_core.filter_block.breadcrumb' => 'onFilterBreadcrumb',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
        );
    }
    
	/**
	 * Ajoute dans la première ligne du pied de page un lien vers la page 
	 * de mentions légales.
	 * 
	 * @param FilterMenuEvent $event
	 */
	public function onFilterFooter1(FilterMenuEvent $event)
	{
		$event
			->getRoot()
			->addChild('Mentions légales', array(
				'uri'    => '/aide/page-19-mentions-legales.html',
				'weight' => 30,
			));
	}
	
	/**
	 * Ajoute un lien vers le module d'aide à gauche du fil d'Ariane.
	 *
	 * @param FilterContentEvent $event
	 */
	public function onFilterBreadcrumb(FilterContentEvent $event)
	{
		if (verifier('aide_voir') && $event->getTemplate() === 'legacy')
    	{
    		$event->setContent(str_replace(
				'Vous êtes ici', 
				'<a href="/aide/" title="Centre d\'aide" class="centre_aide"><img src="'
	    		 .'/img/misc/aide.png" alt="Centre d\'aide" /></a> Vous êtes ici',
				$event->getContent()
			));
    	}
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/aide/', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.3',
		));
		foreach (\Doctrine_Core::getTable('Aide')->getAllId() as $page)
		{
			$event->addLink(URL_SITE.'/aide/page-'.$page['id'].'-'.rewrite($page['titre']).'.html', array(
				'changefreq' => 'monthly',
				'priority'	 => '0.3',
			));
		}
	}
}