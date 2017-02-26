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

namespace Zco\Bundle\ZcorrectionBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;

class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	/**
	 * {@inheritdoc}
	 */
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
		    ->getChild('zCorrection')
		    ->getChild('Documents');
		
		$nombreSoumissions = $this->container->get('zco_admin.manager')->get('zcorrection');
			
		$tab->addChild('Voir les documents en attente', array(
			'label' => 'Il y a '.$nombreSoumissions.' document'.pluriel($nombreSoumissions).' en attente d\'un correcteur',
			'credentials' => array('or', 'zcorriger', 'voir_tutos_attente'),
			'uri' => '/zcorrection/',
			'count' => $nombreSoumissions,
		));
	
		$tab->addChild('Voir les documents actifs', array(
			'credentials' => array('or', 'zcorrection_retirer', 'zcorrection_supprimer', 'zcorrection_priorite', 'zcorrection_editer_tutos'), 
			'uri' => '/zcorrection/gestion.html',
		));
	
		$tab->addChild('Voir les documents en cours de correction', array(
			'credentials' => 'voir_tutos_corriges', 
			'uri' => '/zcorrection/corrections.html?is_zcorrecting=1',
		));
	
		$tab->addChild('Voir les documents corrigés', array(
			'credentials' => 'voir_tutos_corriges', 
			'uri' => '/zcorrection/corrections.html?zcorrected=1',
		));
	}
}