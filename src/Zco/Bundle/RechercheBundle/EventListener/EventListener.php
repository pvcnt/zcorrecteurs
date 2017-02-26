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

namespace Zco\Bundle\RechercheBundle\EventListener;

use Zco\Component\Templating\Event\FilterContentEvent;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_block.speedbarre' => 'onFilterSpeedbarre',
			PagesEvents::SITEMAP        => 'onFilterSitemap',
		);
	}
	
	public function onFilterSpeedbarre(FilterContentEvent $event)
	{
		$module = $event->getRequest()->attributes->get('_module');
		$section = $module === 'blog' ? 'blog' : ($module === 'twitter' ? 'twitter' : 'forum');

		$html = <<<HTML
	<form class="navbar-search pull-right form-search" id="search" method="get" action="/recherche/">
		<input type="hidden" name="section" value="$section" />
		<input type="text" name="recherche" id="recherche" class="search search-query pull-left" placeholder="Rechercher…" />
		<input type="submit" class="submit" value="Rechercher" style="display:none" />
	</form>
HTML;
		if ($event->getTemplate() === 'legacy')
		{
			$html = '<div class="liens_droite">'.$html.'</div>';
			$html = str_replace(
				'</form>', 
				'<a href="/recherche/" onclick="if(\$chk(\$(\'recherche\').value &amp;&amp; \$(\'recherche\').clique)){ document.location=\'/recherche/?recherche=\'+\$(\'recherche\').value+\'&amp;avancee=1\'; return false; }">'.
					'<img src="/img/misc/ajouter.png" title="Recherche avancée" alt="Recherche avancée" />'.
				'</a></form>', $html
			);
		}
		
		$event->setContent($event->getContent().$html);
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/recherche/', array(
			'changefreq' => 'monthly',
			'priority'	 => '0.5',
		));
	}
}