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

namespace Zco\Bundle\EvolutionBundle\EventListener;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterContentEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener extends ContainerAware implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			AdminEvents::MENU => 'onFilterAdmin',
			'zco_core.filter_block.breadcrumb' => 'onFilterBreadcrumb',
		);
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $tab = $event
	        ->getRoot()
	        ->getChild('Contenu')
	        ->getChild('Communication');
		
		$tab->addChild('Liste des retours d\'expérience', array(
			'uri' => '/evolution/liste-retours-experience.html',
		))->secure('evolution_voir_retours');
	}
	
	public function onFilterBreadcrumb(FilterContentEvent $event)
	{
		if (verifier('tracker_voir') && $event->getTemplate() === 'legacy')
    	{
    		$event->setContent(str_replace(
				'<p class="arianne">', 
				'<p class="arianne"><span style="float: right;">' .
    				'<a href="/evolution/">' .
    				'<img src="/pix.gif" class="fff bug" alt="" /> Signaler une anomalie'.
					(verifier('tracker_etre_assigne') ? 
						' ('.$this->container->get('zco_admin.manager')->get('demandes').
						' - '.$this->container->get('zco_admin.manager')->get('taches').')'
						: ''
					).
					'</a>'.
				'</span>',
				$event->getContent()
			));
    	}
	}
}