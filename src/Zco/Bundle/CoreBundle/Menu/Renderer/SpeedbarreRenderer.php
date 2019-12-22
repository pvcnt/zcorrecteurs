<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
 * Moteur de rendu pour la barre de navigation rapide.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SpeedbarreRenderer extends ListRenderer
{
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

		$html = '';
		$html  = $this->format('<ul'.$this->renderHtmlAttributes($attributes).'>', 'ul', $item->getLevel(), $options);
		
		$html .= ($item instanceof MenuItem) ? $item->getHtml('prefix') : '';
		$html .= ($item instanceof MenuItem && $item->getHtml()) ? $item->getHtml() : $this->renderChildren($item, $options);
		$html .= ($item instanceof MenuItem) ? $item->getHtml('suffix') : '';
		
		$html .= $this->format('</ul>', 'ul', $item->getLevel(), $options);

		return $html;
	}
}