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

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;

/**
 * Observateur principal pour le module d'options.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			AdminEvents::MENU                       => 'onFilterAdmin',
			CoreEvents::DAILY_CRON                  => 'onDailyCron',
		);
	}
	
	/**
	 * Ajoute des liens sur le panneau d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
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

	/**
	 * Met à jour les absences chaque jour.
	 *
	 * @param CronEvent $event
	 */
	public function onDailyCron(CronEvent $event)
	{
		\Doctrine_Core::getTable('Utilisateur')->purgeAbsences();
	}
}