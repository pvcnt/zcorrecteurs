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

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterContentEvent;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
//			'zco_core.filter_menu.speedbarre_right' => 'onFilterSpeedbarreRight',
//			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
			AdminEvents::MENU => 'onFilterAdmin',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Initialise le code javascript nécessaire à l'affichage du lien vers la 
	 * page de dons.
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
		$event->initBehavior('popover', array('selector' => '#faire-un-don', 'options' => array('placement' => 'bottom')));
	}
	
	public function onFilterSpeedbarreRight(FilterMenuEvent $event)
	{
		$event->getRoot()->addChild('Faire un don', array(
			'uri' => '/dons/',
			'linkAttributes' => array(
				'title' => 'Faire un don &rarr;', 
				'data-content' => 'Le site et son association ont de nombreux nouveaux projets. Découvrez dès maintenant lesquels et comment vous pouvez les soutenir !',
				'id' => 'faire-un-don',
				'rel' => 'popover',
			),
			'label' => '<img src="/img/aide/argent.png" />',
		));
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $tab = $event
	        ->getRoot()
	        ->getChild('Gestion financière')
	        ->getChild('Dons');
		
		$tab->addChild('Ajouter un don', array(
			'uri' => '/dons/ajouter.html'
		))->secure('dons_ajouter');
		
		$tab->addChild('Voir tous les dons', array(
			'uri' => '/dons/gestion.html'
		))->secure(array('or', 'dons_ajouter', 'dons_editer', 'dons_supprimer'));
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