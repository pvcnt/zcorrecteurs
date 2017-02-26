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

namespace Zco\Bundle\BlogBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\Templating\TemplatingEvents;

/**
 * Observateur principal pour le module du blog.
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
			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
			TemplatingEvents::FILTER_VARIABLES => 'onTemplatingFilterVariables',
			PagesEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Référence sur toutes les pages le flux RSS du blog.
	 *
	 * @param FilterResourcesEvent $event
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
		$event->addFeed('/blog/flux.html', array('title' => 'Derniers billets du blog'));
	}
	
	/**
	 * Ajoute des variables communes à toutes les pages du module de blog.
	 *
	 * @param FilterVariablesEvent $event
	 */
	public function onTemplatingFilterVariables(FilterVariablesEvent $event)
	{
		if ($this->container->get('request')->attributes->get('_module') !== 'blog')
		{
			return;
		}
		
		\Config::load('messages');
		$config = \Config::get('messages');

		$event->add('BlogStatuts', $config['BlogStatuts']);
		$event->add('AuteursClass', array(3 => 'gras', 2 => 'normal', 1 => 'italique'));
		$event->add('Etats', array(
			BLOG_BROUILLON => 'Brouillon',
			BLOG_PREPARATION => 'En cours de préparation',
			BLOG_PROPOSE => 'Proposé',
			BLOG_REFUSE => 'Refusé',
			BLOG_VALIDE => 'Validé'
		));
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		include_once(__DIR__.'/../modeles/blog.php');
		
		$event->addLink(URL_SITE.'/blog/', array(
			'changefreq' => 'weekly',
			'priority'	 => '0.6',
		));
		foreach (ListerBilletsId() as $billet)
		{
			$event->addLink(URL_SITE.'/blog/billet-'.$billet['blog_id'].'-'.rewrite($billet['version_titre']).'.html', array(
				'changefreq' => 'weekly',
				'priority'	 => '0.7',
			));
		}
	}
}