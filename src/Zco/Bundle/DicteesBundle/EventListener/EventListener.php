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

namespace Zco\Bundle\DicteesBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\DicteesBundle\Domain\Dictation;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\Templating\TemplatingEvents;

class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	static public function getSubscribedEvents()
	{
		return array(
			TemplatingEvents::FILTER_VARIABLES => 'onTemplatingFilterVariables',
			AdminEvents::MENU => 'onFilterAdmin',
			PagesEvents::SITEMAP => 'onFilterSitemap',
		);
	}
		
	public function onTemplatingFilterVariables(FilterVariablesEvent $event)
	{
		if (
			!$this->container->get('request')->attributes->has('_module') ||
			$this->container->get('request')->attributes->get('_module') !== 'dictees'
		)
		{
			return;
		}

		$event->add('DicteeDifficultes', Dictation::LEVELS);
		$event->add('DicteeEtats', Dictation::STATUSES);
		$event->add('DicteeCouleurs', Dictation::COLORS);
	}
	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
			->getRoot()
			->getChild('Contenu')
			->getChild('Dictées');
		
		$NombreDicteesProposees = $this->container->get('zco_admin.manager')->get('dictees');
		
		$tab->addChild('Voir les dictées proposées', array(
			'label' => 'Il y a '.$NombreDicteesProposees.' dictée'.pluriel($NombreDicteesProposees).' proposée'.pluriel($NombreDicteesProposees),
			'uri' => '/dictees/propositions.html', 
			'count' => $NombreDicteesProposees,
		))->secure('dictees_publier');
		
		$tab->addChild('Ajouter une dictée', array(
			'uri' => '/dictees/ajouter.html',
		))->secure('dictees_ajouter');
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/dictees/', array(
			'changefreq' => 'weekly',
			'priority'	 => '0.6',
		));
		foreach (\Doctrine_Core::getTable('Dictee')->getAllId() as $dictee)
		{
			$event->addLink(URL_SITE.'/dictees/dictee-'.$dictee['id'].'-'.rewrite($dictee['titre']).'.html', array(
				'changefreq' => 'monthly',
				'priority'	 => '0.5',
			));
		}
	}
}