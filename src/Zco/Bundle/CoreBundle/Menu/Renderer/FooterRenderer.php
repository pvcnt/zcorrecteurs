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
 * Moteur de rendu pour une ligne de liens présents dans le pied de page du 
 * site. Chaque ligne est représentée par un menu. Son rendu est effectué comme 
 * une succession de liens dans un paragraphe.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FooterRenderer extends ListRenderer
{
	protected $separator = ' | ';
	
	/**
	 * Définit le séparateur entre chaque lien de la ligne.
	 *
	 * @param string $separator
	 */
	public function setSeparator($separator)
	{
		$this->separator = $separator;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function renderList(ItemInterface $item, array $attributes, array $options)
	{
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

		$html  = '<p'.$this->renderHtmlAttributes($attributes).'>';
		$html .= ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
		foreach ($item->getChildren() as $child)
		{
			$html .= $this->renderItem($child, $options);
		}
		$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
		$html .= '</p>';

		return $html;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function renderItem(ItemInterface $item, array $options)
	{
		// if we don't have access or this item is marked to not be shown
		if (!$item->isDisplayed())
		{
			return '';
		}

		if ($item instanceof MenuItem && $item->getHtml())
		{
			return $item->getHtml();
		}

		// explode the class string into an array of classes
		$class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

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
		if (!empty($class))
		{
			$attributes['class'] = implode(' ', $class);
		}

		$html = ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
		$html .= $this->renderLink($item, $options);	
		$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
		
		if (!$item->actsLikeLast())
		{
			$html .= $this->separator;
		}

		return $html;
	}
}
