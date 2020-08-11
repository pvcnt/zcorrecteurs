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

namespace Zco\Bundle\EvolutionBundle;

use Zco\Component\HttpKernel\Bundle\AbstractBundle;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;

class ZcoEvolutionBundle extends AbstractBundle
{
	public function preload()
	{
		//Compteurs affichés à droite du fil d'Ariane.
		$this->container->get('zco_admin.manager')->register('taches', 'tracker_etre_assigne', array('count' => false));
		$this->container->get('zco_admin.manager')->register('demandes', 'tracker_etre_assigne', array('count' => false));
	}
	
	public function load()
	{
		$this->container->get('zco_vitesse.resource_manager')->requireResource(
		    '@ZcoEvolutionBundle/Resources/public/css/demandes.css'
		);
		
		//Inclusion des modèles
		include(__DIR__.'/modeles/tickets.php');
		include(__DIR__.'/modeles/reponses.php');
		
		$this->container->get('event_dispatcher')->addListener(TemplatingEvents::FILTER_VARIABLES, function(FilterVariablesEvent $event)
		{
			$event->add('TicketsPriorites', array(
				1 => array(
					'priorite_nom' => 'Très basse',
					'priorite_class' => 'gris',
				),
				2 => array(
					'priorite_nom' => 'Basse',
					'priorite_class' => 'vertf',
				),
				3 => array(
					'priorite_nom' => 'Moyenne',
					'priorite_class' => 'orange',
				),
				4 => array(
					'priorite_nom' => 'Haute',
					'priorite_class' => 'rouge',
				),
				5 => array(
					'priorite_nom' => 'Urgente',
					'priorite_class' => 'rouge gras',
				),
			));

			$event->add('TicketsEtats', array(
				1 => array(
					'etat_nom' => 'Nouveau',
					'etat_class' => 'marron',
				),
				2 => array(
					'etat_nom' => 'Confirmé',
					'etat_class' => 'rouge',
				),
				3 => array(
					'etat_nom' => 'Incomplet',
					'etat_class' => 'orange',
				),
				4 => array(
					'etat_nom' => 'N\'est pas une anomalie',
					'etat_class' => 'gris',
				),
				5 => array(
					'etat_nom' => 'Rejeté',
					'etat_class' => 'gris',
				),
				6 => array(
					'etat_nom' => 'En cours',
					'etat_class' => 'marron',
				),
				8 => array(
					'etat_nom' => 'Impossible à reproduire',
					'etat_class' => 'gris',
				),
				10 => array(
					'etat_nom' => 'À l\'étude',
					'etat_class' => 'marron',
				),
				9 => array(
					'etat_nom' => 'Résolu en local',
					'etat_class' => 'vertf',
				),
				7 => array(
					'etat_nom' => 'Résolu',
					'etat_class' => 'vertf',
				),
			));
		});
	}
}