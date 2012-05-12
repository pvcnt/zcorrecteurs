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

namespace Zco\Bundle\CoreBundle\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Zco\Bundle\CoreBundle\Menu\MenuItem;

/**
 * Renders MenuItem tree as unordered list
 */
class LeftMenuRenderer extends ListRenderer
{
    /**
     * @see RendererInterface::render
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren())
		{
            return '';
        }

        $html = '';
		$options['depth'] = 1;
        foreach ($item->getChildren() as $child)
		{
            $html .= $this->renderItem($child, $options);
        }

        return $html;
    }
	
    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     *
     * @param \Knp\Menu\ItemInterface $item
     * @param array $options The options to render the item
     * @return string
     */
    public function renderItem(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

		if ($options['depth'] === 1)
		{
			$class[] = 'bloc';
			$class[] = str_replace('-', '', rewrite($item->getName()));
		}

        if ($item->isCurrent())
		{
            $class[] = $options['currentClass'];
        }
		elseif ($item->isCurrentAncestor())
		{
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst())
		{
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast())
		{
            $class[] = $options['lastClass'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

		$html = '';

        // opening block
		if ($options['depth'] === 1)
		{
        	$html .= $this->format('<div'.$this->renderHtmlAttributes($attributes).'>', 'div', $item->getLevel());
			$html .= '<h4>'.$item->getName().'</h4>';
			$html .= ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
			$html .= '<ul class="nav nav-list">';
		
			$options['depth'] = 2;
			foreach ($item->getChildren() as $child)
			{
				$html .= $this->renderItem($child, $options);
			}
			
			$html .= '</ul>';
			$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
			$html .= $this->format('</div>', 'div', $item->getLevel());
		}
		else
		{
			$html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel());
			$html .= ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
			
			$html .= $this->renderLink($item, $options);
			
			$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
			$html .= $this->format('</li>', 'li', $item->getLevel());
		}

        return $html;
    }
}