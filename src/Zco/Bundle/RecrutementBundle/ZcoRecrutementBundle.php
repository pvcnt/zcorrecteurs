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

namespace Zco\Bundle\RecrutementBundle;

use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\HttpKernel\Bundle\AbstractBundle;

class ZcoRecrutementBundle extends AbstractBundle
{
	public function preload()
	{
		//Enregistrement du compteur de tâches admin.
		$this->container->get('zco_admin.manager')->register('recrutement', 'recrutements_repondre');
	}
	
	public function load()
	{
		//Inclusion des modèles
		include(__DIR__.'/modeles/candidatures.php');
		include_once(__DIR__.'/modeles/recrutements.php');

		$this->container->get('event_dispatcher')->addListener(TemplatingEvents::FILTER_VARIABLES, function(FilterVariablesEvent $event)
		{
			$event->add('avisType', array(
				array(
					'nom' => 'Oui',
					'couleur' => '#71b047'
				),
				array(
					'nom' => 'Non',
					'couleur' => '#ef4e4e'
				),
				array(
					'nom' => 'Réservé',
					'couleur' => '#f1a521'
				),
				array(
					'nom' => 'Sans avis',
					'couleur' => '#21a5f1'
				),
			));
		});
	}	
}