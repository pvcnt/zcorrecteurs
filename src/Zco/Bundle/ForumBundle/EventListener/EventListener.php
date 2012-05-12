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

namespace Zco\Bundle\ForumBundle\EventListener;

use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur principal pour le module du forum.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
			AdminEvents::MENU => 'onFilterAdmin',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Ajoute la feuille de style CSS du forum sur toutes les actions du module.
	 *
	 * @param FilterResourcesEvent $event
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
		if (
		    $this->container->get('request')->attributes->has('_module') && 
		    $this->container->get('request')->attributes->get('_module') === 'forum'
		)
		{
			$event->requireResource('@ZcoForumBundle/Resources/public/css/forum.css');
		}
	}
	
	/**
	 * Ajoute le lien vers le forum dans la barre de navigation rapide.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarre(FilterMenuEvent $event)
	{
		$event
			->getRoot()
			->addChild('Forum', array('uri'   => '/forum/', 'weight' => 20))
		 	->setCurrent(
			    $event->getRequest()->attributes->has('_module') && 
			    $event->getRequest()->attributes->get('_module') === 'forum'
			);
	}
	
	/**
	 * Ajoute les liens vers les pages d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Contenu')
		    ->getChild('Forums');
		
		$tasks = $this->container->get('zco_admin.manager')->get('alertes');
		$tab->addChild('Voir les alertes non résolues', array(
			'label' => 'Il y a '.$tasks.' alerte non résolue'.pluriel($tasks),
			'uri' => '/forum/alertes.html',
			'count' => $tasks,
		))->secure('voir_alertes');
		
		$tab->addChild('Gérer les sujets en coup de cœur', array(
			'uri' => '/forum/sujets-coups-coeur.html',
			'separator' => true,
		))->secure('mettre_sujets_coup_coeur');
		
		$tab->addChild('Ajouter un message automatique', array(
		    'uri' => '/forum/ajouter-message-auto.html',
		))->secure('gerer_mess_autos');
		
		$tab->addChild('Gérer les messages automatiques', array(
			'uri' => '/forum/gestion-messages-auto.html',
		))->secure('gerer_mess_autos');
		
		$tab = $event
		    ->getRoot()
		    ->getChild('Informations')
		    ->getChild('Statistiques générales');
		
		$tab->addChild('Statistiques temporelles du forum', array(
			'uri' => '/forum/statistiques-temporelles.html',
			'separator' => true,
			'weight' => 70,
		))->secure('forum_stats_generales');
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		include_once(__DIR__.'/../modeles/forums.php');
		
		$event->addLink(URL_SITE.'/forum/', array(
			'changefreq' => 'daily',
			'priority'	 => '0.7',
		));
		foreach (ListerSujetsId(array(34,45,42,43,44,46,47,91,92,93,94,178)) as $topic)
		{
			$event->addLink(URL_SITE.'/forum/sujet-'.$topic['sujet_id'].'-'.rewrite($topic['sujet_titre']).'.html', array(
				'changefreq' => 'weekly',
				'priority'	 => '0.5',
			));
		}
	}
}