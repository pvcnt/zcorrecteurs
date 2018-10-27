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

namespace Zco\Bundle\RecrutementBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Zco\Bundle\RecrutementBundle\Admin\ApplicationsPendingTask;

/**
 * Observateur principal pour le module de recrutement.
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
			AdminEvents::MENU => 'onFilterAdmin',
			PagesEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Ajoute les liens vers les pages d'administration du module de recrutement.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event->getRoot()->getChild('Recrutements');
		
		$nombreCandidatures = $this->container->get('zco.admin')->get(ApplicationsPendingTask::class);
	
		$tab->addChild('Voir les candidatures en attente', array(
			'label' => 'Il y a ' . $nombreCandidatures . ' candidature' . pluriel($nombreCandidatures) . ' en attente',
			'credentials' => array('recrutements_voir_candidatures'),
			'uri' => '/recrutement/gestion.html',
			'count' => $nombreCandidatures,
		));
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		include_once(__DIR__.'/../modeles/recrutements.php');
		
		$event->addLink(URL_SITE.'/recrutement/', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.4',
		));
		foreach (ListerRecrutementsSitemap() as $recrut)
		{
			$event->addLink(URL_SITE.'/recrutement/recrutement-'.$recrut['recrutement_id'].'-'.rewrite($recrut['recrutement_nom']).'.html', array(
				'changefreq' => 'monthly',
				'priority'	 => '0.3',
			));
		}
	}
}