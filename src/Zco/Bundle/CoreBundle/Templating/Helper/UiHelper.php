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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Zco\Bundle\CoreBundle\Menu\Renderer\SpeedbarreRenderer;
use Zco\Bundle\CoreBundle\Menu\Renderer\LeftMenuRenderer;
use Zco\Bundle\CoreBundle\Menu\Renderer\FooterRenderer;
use Zco\Bundle\CoreBundle\Menu\MenuFactory;
use Zco\Bundle\CoreBundle\View\ViewInterface;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterContentEvent;
use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ensemble de fonctions ayant pour objet d'effectuer le rendu de certaines 
 * zones de l'interface de site. Ces zones pourront être manipulées par 
 * l'intermédiaire d'événements par l'ensembles de bundles afin de modifier 
 * le rendu.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UiHelper extends Helper
{
	private $container;
	
	/**
	 * Constructeur.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	/**
	 * Effectue le rendu du menu de gauche du site.
	 *
	 * @return string Code HTML
	 */
	public function leftMenu($template)
	{
		$factory = new MenuFactory();
		$menu = $factory->createItem('left_menu');
		
		return $this->renderMenu($menu, new LeftMenuRenderer(), $template);
	}
	
	/**
	 * Effectue le rendu du menu de la barre de navigation rapide du site.
	 *
	 * @return string Code HTML
	 */
	public function speedbarre($template)
	{
		$factory = new MenuFactory();
		$menu = $factory->createItem('speedbarre', array('attributes' => array('class' => 'nav')));
		
		return $this->renderMenu($menu, new SpeedbarreRenderer(), $template);
	}
	
	public function speedbarreRight($template)
	{
		$factory = new MenuFactory();
		$menu = $factory->createItem('speedbarre_right', array('attributes' => array('class' => 'nav pull-right')));
		
		return $this->renderMenu($menu, new SpeedbarreRenderer(), $template);
	}
	
	/**
	 * Effectue le rendu du fil d'Ariane.
	 *
	 * @return string Code HTML
	 */
	public function breadcrumb($template)
	{
		$fil  = ($template === 'legacy') ? '<p class="arianne">' : '<ul class="breadcrumb">';
		$fil .= ($template === 'legacy') ? 'Vous êtes ici : ' : '';
		$fil .= implode(($template === 'legacy') ? ' &gt; ' : '<span class="divider">»</span>', \Page::$fil_ariane);
		$fil .= ($template === 'legacy') ? '</p>' : '</ul>';

		return $this->filterBlock($fil, 'breadcrumb', $template);
	}
	
	/**
	 * Effectue le rendu du menu de la zone de contenu en haut à droite de la 
	 * bannière du site.
	 *
	 * @return string Code HTML
	 */
	public function headerRight($template)
	{
		return $this->filterBlock('', 'header_right', $template);
	}
	
	/**
	 * Effectue le rendu d'une ligne du pied de page du site.
	 *
	 * @return string Code HTML
	 */
	public function footer($line, array $options = array())
	{
		$factory = new MenuFactory();
		$menu = $factory->createItem('footer'.$line, $options);
		
		$renderer = new FooterRenderer();
		$renderer->setSeparator($line > 1 ? ' - ' : ' | ');
		
		return $this->renderMenu($menu, $renderer);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'ui';
	}
	
	public function render(ViewInterface $view, array $options = array())
	{
		return $view->render($options);
	}
	
	/**
	 * Effectue le rendu d'un menu. Le menu est filtré deux fois : une fois en 
	 * tant qu'arbre d'objets et une seconde fois en tant que code HTML.
	 *
	 * @param ItemInterface $menu Le menu à afficher
	 * @param RendererInterface $renderer La stratégie d'affichage du menu
	 * @param string $template Le nom du layout dans lequel le menu est inclus
	 * @return string Code HTML
	 */
	private function renderMenu(ItemInterface $menu, RendererInterface $renderer, $template = null)
	{
		$this->filterMenu($menu, $template);
		
		if ($menu->hasChildren())
		{
			$menu->reorderChildren(null);
			$content = $renderer->render($menu);
		}
		else
		{
			$content = '';
		}
		
		return $this->filterBlock($content, $menu->getName(), $template);
	}
	
	/**
	 * Filtre un menu.
	 *
	 * @param ItemInterface $menu Le menu à afficher
 	 * @param string $template Le nom du layout dans lequel le menu est inclus
	 */
	private function filterMenu(ItemInterface $menu, $template = null)
	{
		$event = new FilterMenuEvent($this->container->get('request'), $menu, $template);
		$this->container->get('event_dispatcher')->dispatch('zco_core.filter_menu.'.$menu->getName(), $event);
	}
	
	/**
	 * Filtre un bloc (sous forme de code HTML).
	 *
	 * @param  string $content Le contenu du bloc
	 * @param  string $block Le nom du bloc
 	 * @param  string $template Le nom du layout dans lequel le bloc est inclus
 	 * @param  integer|false $lifetime Durée de mise en cache du bloc, false pour désactiver
	 * @return string
	 */
	private function filterBlock($content, $block, $template = null, $lifetime = false)
	{
		$event = new FilterContentEvent($this->container->get('request'), $content, $template);
		$this->container->get('event_dispatcher')->dispatch('zco_core.filter_block.'.$block, $event);
		
		if ($lifetime !== false)
		{
			$cache = $this->container->get('zco_core.cache');
			$cache->set('zco_core.ui.block.'.$block, $event->getContent(), $lifetime);
		}
		
		return $event->getContent();
	}
	
	private function checkCache($block)
	{
		return $this->container->get('zco_core.cache')->get('zco_core.ui.block.'.$block);
	}
}