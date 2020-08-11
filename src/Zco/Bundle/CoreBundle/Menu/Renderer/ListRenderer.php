<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Knp\Menu\Renderer\ListRenderer as BaseListRenderer;
use Knp\Menu\ItemInterface;
use Zco\Bundle\CoreBundle\Menu\MenuItem;

/**
 * Extension du moteur de rendu de base affichant une série de listes 
 * imbriquées. Celui-ci supporte les extensions nécessaires au bon affichage
 * des liens sur le site (compteurs de tâches, séparateurs, droits d'accès...).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class ListRenderer extends BaseListRenderer
{
	/**
	 * {@inheritdoc}
	 */
	protected function renderLink(ItemInterface $item, array $options = array())
	{
		$label = $item->getLabel().(!is_null($item->getCount()) ? ' <span class="badge nb_speedbare">'.$item->getCount().'</span>' : '');
		
		$html = '';
		
		if ($item instanceof MenuItem && $item->getHtml())
		{
			$html = $item->getHtml();
		}
		elseif ($item->getUri() && (!$item->isCurrent() || $options['currentAsLink']))
		{
			$html .= sprintf(
				'<a href="%s"%s>%s</a>', 
				$this->escape($item->getUri()), 
				$this->renderHtmlAttributes($item->getLinkAttributes()),
				$label
			);
		}
		else
		{
			$html .= sprintf(
				'<span%s>%s</span>', 
				$this->renderHtmlAttributes($item->getLinkAttributes()),
				$item->getLabel()
			);
		}

		return $this->format($html, 'link', $item->getLevel(), $options);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function format($html, $type, $level, array $options)
	{
		if ($options['compressed'])
		{
			return $html;
		}

		switch ($type)
		{
			case 'ul':
			case 'link':
			case 'div':
				$spacing = $level * 4;
				break;

			case 'li':
				$spacing = $level * 4 - 2;
				break;
		}

		return str_repeat(' ', $spacing).$html."\n";
	}
}