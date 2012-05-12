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

namespace Zco\Bundle\AnnoncesBundle\EventListener;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
		    AdminEvents::MENU => 'onFilterAdmin',
			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
		);
	}
	
	/**
	 * Ajoute le code javascript nécessaire au chargement asynchrone des bannières.
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
	    $config = array(
	        'categorie' => GetIDCategorieCourante(),
	        'page'      => $_SERVER['REQUEST_URI'],
        );
	    
	    if (!empty($_GET['_annonce']))
	    {
	        $config['annonce'] = (int) $_GET['_annonce'];
	    }
	    if (!empty($_GET['_pays']) && verifier('annonces_publier'))
	    {
	        $config['pays'] = (string) $_GET['_pays'];
	    }
	    if (isset($_GET['_groupe']) && verifier('annonces_publier'))
	    {
	        $config['groupe'] = (int) $_GET['_groupe'];
	    }
	    
	    $event->initBehavior('annonces-inject-banner', $config);
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $event
	        ->getRoot()
	        ->getChild('Contenu')
	        ->getChild('Communication')
	        ->addChild('Modifier les annonces globales au site', array(
	            'uri' => '/annonces/',
	        ))
	        ->secure(array('or', 'annonces_ajouter', 'annonces_modifier', 'annonces_supprimer', 'annonces_publier'));
	}
}