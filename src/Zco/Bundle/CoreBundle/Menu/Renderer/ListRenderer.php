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

use Knp\Menu\Renderer\ListRenderer as BaseListRenderer;
use Knp\Menu\ItemInterface;
use Zco\Bundle\CoreBundle\Menu\MenuItem;

abstract class ListRenderer extends BaseListRenderer
{
	/**
	 * Renders the link in a a tag with link attributes or
	 * the label in a span tag with label attributes
	 *
	 * Tests if item has a an uri and if not tests if it's
	 * the current item and if the text has to be rendered
	 * as a link or not.
	 *
	 * @param \Knp\Menu\ItemInterface $item The item to render the link or label for
	 * @param array $options The options to render the item
	 * @return string
	 */
	public function renderLink(ItemInterface $item, array $options = array())
	{
		$options = array_merge($this->getDefaultOptions(), $options);
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

		return $this->format($html, 'link', $item->getLevel());
	}
	
	/**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param  string $html The html to render in an (un)formatted way
     * @param  string $type The type [ul,link,li] of thing being rendered
     * @param integer $level
     * @return string
     */
    protected function format($html, $type, $level)
    {
        if ($this->renderCompressed)
        {
            return $html;
        }

        switch ($type) {
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