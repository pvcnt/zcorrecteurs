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

namespace Zco\Bundle\CategoriesBundle\EventListener;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			AdminEvents::MENU => 'onFilterAdmin',
		);
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Gestion technique')
		    ->getChild('Catégories');
		
		$tab->addChild('Ajouter une catégorie', array(
			'uri' => '/categories/ajouter.html',
		))->secure('cats_ajouter');
		
		$tab->addChild('Gérer les catégories', array(
			'uri' => '/categories/',
		))->secure(array('or', 'cats_ordonner', 'cats_editer', 'cats_supprimer'));
		
		$tab->addChild('Afficher un graphique des catégories', array(
			'uri' => '/categories/image.html'
		))->secure(array('or', 'cats_ordonner', 'cats_editer', 'cats_supprimer'));
	}
}