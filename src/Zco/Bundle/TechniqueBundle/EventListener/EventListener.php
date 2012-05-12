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

namespace Zco\Bundle\TechniqueBundle\EventListener;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
			KernelEvents::REQUEST => 'onKernelRequest',
		);
	}
	
	/**
	 * Gère la substitution d'un administrateur ou un développeur vers un 
	 * autre groupe (pour tester ses droits).
	 *
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
	    if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
	    {
            return;
        }
        
		//Gestion de la substitution admin.
		if ((isset($_SESSION['debug_groupe']) || verifier('code')) && isset($_GET['_changer_groupe']))
		{
			$vraiGroupe = $this->getRealGroup();
			$groupesAccessibles = $this->getSubstituteGroups();

            //Si on demande à repasser dans son groupe d'origine.
			if (isset($_SESSION['debug_groupe']) &&
				($_GET['_changer_groupe'] < 0 || $_GET['_changer_groupe'] == $vraiGroupe)
			)
			{
				$_SESSION['groupe'] = $_SESSION['debug_groupe']['principal'];
				$_SESSION['groupes_secondaires'] = $_SESSION['debug_groupe']['secondaires'];
				unset($_SESSION['debug_groupe']);
			}
			//Si on demande à passer dans un groupe accessible.
			elseif (isset($groupesAccessibles[$_GET['_changer_groupe']]))
			{
				if (!isset($_SESSION['debug_groupe']))
				{
					$_SESSION['debug_groupe'] = array(
					    'principal' => $_SESSION['groupe'],
					    'secondaires' => $_SESSION['groupes_secondaires']
					);
				}
				$_SESSION['groupe'] = $_GET['_changer_groupe'];
				$_SESSION['groupes_secondaires'] = array();
			}
			
			$event->setResponse(new RedirectResponse(
			    str_replace('_changer_groupe='.$_GET['_changer_groupe'], '', $_SERVER['REQUEST_URI'])
			));
		}
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Gestion technique')
		    ->getChild('Modifications en ligne');
		    
		$tab->addChild('Modifier la configuration', array(
			'credentials' => 'gerer_config', 
			'uri' => '/technique/configuration.html',
		));
	
		$tab->addChild('Gestion des caches', array(
			'credentials' => 'gerer_caches', 
			'uri' => '/technique/gestion-caches.html',
		));
	
		$tab->addChild('État d\'APC', array(
			'credentials' => 'gerer_caches', 
			'uri' => '/technique/apc.html',
		));
	}
	
	private function getRealGroup()
	{
		return isset($_SESSION['debug_groupe']['principal']) ? $_SESSION['debug_groupe']['principal'] : $_SESSION['groupe'];
	}
	
	private function getSubstituteGroups()
	{
		static $substituteGroups;
		
		if (isset($substituteGroups))
		{
		    return $substituteGroups;
		}
		
		$realGroup = $this->getRealGroup();
		if ($realGroup == GROUPE_ADMINISTRATEURS && (isset($_SESSION['debug_groupe']) || verifier('code')))
		{
			foreach (ListerGroupes() as $grp)
			{
				$substituteGroups[$grp['groupe_id']] = $grp['groupe_nom'];
			}
		}
		elseif ($realGroup == GROUPE_DEVELOPPEURS)
		{
			$substituteGroups = array(
				GROUPE_VISITEURS => 'Visiteurs',
				GROUPE_DEFAUT => 'Membres',
				GROUPE_DEVELOPPEURS => 'Développeurs'
			);
		}
		else
		{
			$substituteGroups = array();
		}
		
		return $substituteGroups;
	}
}