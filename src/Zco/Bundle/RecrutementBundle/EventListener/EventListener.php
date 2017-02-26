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
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;

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
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
			AdminEvents::MENU => 'onFilterAdmin',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Ajoute un lien vers le module de recrutement dans le menu de gauche.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		$event->getRoot()->getChild('Communauté')->addChild('Nous rejoindre', array(
			'uri'    => '/recrutement/',
			'weight' => 0,
			'linkAttributes' => array(
				'style' => 'font-weight: bold;',
				'rel'   => 'Vous souhaitez nous rejoindre ? Postulez sur notre module de recrutement.', 
				'title' => 'Recrutement',
			),
		));
	}
	
	/**
	 * Ajoute les liens vers les pages d'administration du module de recrutement.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Communauté')
		    ->getChild('Recrutements');
		
		$nombreCandidatures = $this->container->get('zco_admin.manager')->get('recrutement');
		
		$tab->addChild('Ajouter un recrutement', array(
			'credentials' => 'recrutements_ajouter', 
			'uri' => '/recrutement/ajouter.html',
		));
	
		$tab->addChild('Voir les candidatures en attente', array(
			'label' => 'Il y a ' . $nombreCandidatures . ' candidature' . pluriel($nombreCandidatures) . ' en attente',
			'credentials' => array('and', 'recrutements_voir',
								array('or', 'recrutements_editer', 'recrutements_ajouter',
					  			'recrutements_supprimer', 'recrutements_voir_candidatures', 'recrutements_repondre')),
			'uri' => '/recrutement/gestion.html',
			'count' => $nombreCandidatures,
		));
	
		$tab->addChild('Accéder à l\'espace recrutement', array(
			'credentials' => 'recrutements_voir', 
			'uri' => '/recrutement/',
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