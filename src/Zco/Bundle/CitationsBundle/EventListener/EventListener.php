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

namespace Zco\Bundle\CitationsBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterContentEvent;

class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_block.header_right' => 'onFilterHeaderRight',
			AdminEvents::MENU => 'onFilterAdmin',
		);
	}
	
	public function onFilterHeaderRight(FilterContentEvent $event)
	{
		$cache = $this->container->get('zco_core.cache');
		if (($html = $cache->get('header_citations')) === false)
		{
			$citation = \Doctrine_Core::getTable('Citation')->CitationAleatoire();
			$html = '';
			if (count($citation) > 0)
			{
				$html = render_to_string('ZcoCitationsBundle::citation.html.php', compact('citation'));
			}
			$cache->set('header_citations', $html, 3600);
		}
		
		$event->setContent($html);
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $tab = $event
	        ->getRoot()
	        ->getChild('Contenu')
	        ->getChild('Citations');

		$tab->addChild('Ajouter une citation', array(
			'uri' => '/citations/ajouter.html'
		))->secure('citations_ajouter');
		
		$tab->addChild('Gérer les citations', array(
			'uri' => '/citations/',
		))->secure(array('or', 'citations_modifier', 'citations_supprimer', 'citations_autoriser'));
	}
}