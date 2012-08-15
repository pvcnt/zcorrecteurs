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

namespace Zco\Bundle\OptionsBundle\EventListener;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener extends ContainerAware implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
			'zco_core.filter_menu.speedbarre_right' => 'onFilterSpeedbarreRight',
			AdminEvents::MENU => 'onFilterAdmin',
		);
	}
	
	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		if (!verifier('connecte') || $event->getTemplate() !== 'legacy')
		{
		    return;
	    }
	    
		$event->getRoot()->getChild('Mon compte')->addChild('Mes options', array(
			'uri'    => $this->container->get('router')->generate('zco_options_index'),
			'weight' => 10,
			'linkAttributes' => array(
				'rel'   => 'Changer mon pseudo, mot de passe, avatar, profil, etc., ainsi que les options de navigation et tout ce qui concerne votre compte.', 
				'title' => 'Mes options',
			),
		));
	}
	
	public function onFilterSpeedbarreRight(FilterMenuEvent $event)
	{
		if (!verifier('connecte') || $event->getTemplate() !== 'bootstrap')
		{
		    return;
	    }
	    
		$event->getRoot()->getChild('Mon compte')->addChild('Mes paramètres', array(
			'uri'    => $this->container->get('router')->generate('zco_options_index'),
			'weight' => 20,
		));
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $tab = $event
	        ->getRoot()
	        ->getChild('Gestion technique')
	        ->getChild('Options');
	    
		$tab->addChild('Modifier les options de navigation par défaut', array(
			'uri' => $this->container->get('router')->generate('zco_options_preferences', array('id' => '0')),
		))->secure('options_editer_defaut');
	}
}