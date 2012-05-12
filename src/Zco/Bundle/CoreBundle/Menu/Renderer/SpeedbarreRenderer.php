<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\CoreBundle\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Zco\Bundle\CoreBundle\Menu\MenuItem;

class SpeedbarreRenderer extends ListRenderer
{
	public function render(ItemInterface $item, array $options = array())
	{
		$options = array_merge($this->getDefaultOptions(), $options);

		/**
		 * Return an empty string if any of the following are true:
		 *   a) The menu has no children eligible to be displayed
		 *   b) The depth is 0
		 *   c) This menu item has been explicitly set to hide its children
		 */
		if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
			return '';
		}

		$html = '';
		$html  = $this->format('<ul'.$this->renderHtmlAttributes($item->getAttributes()).'>', 'ul', $item->getLevel());
		
		$html .= ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
		$html .= ($item instanceof MenuItem && $item->getHtml()) ? $item->getHtml() : $this->renderChildren($item, $options);
		$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
		
		$html .= $this->format('</ul>', 'ul', $item->getLevel());

		return $html;
	}
}