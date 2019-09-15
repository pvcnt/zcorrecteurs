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

namespace Zco\Bundle\AdminBundle\Menu;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class MenuRenderer
{
    public function render(MenuItem $item)
    {
        $html = '<table border="0" cellspacing="4" cellpadding="0" width="100%"><tbody><tr><td>';
        $perColumn = ceil(count($item->getChildren()) / 2);
        $i = 0;

        foreach ($item->getChildren() as $section) {
            $this->alter($section);

            if (!$section->isDisplayed()) {
                continue;
            }

            if ($i == $perColumn && $i > 0) {
                $html .= '</td><td>';
            }

            $html .= '<div class="admin_bloc bloc_' . str_replace('-', '_', rewrite($section->getName())) . '">'
                . '<img src="/pix.gif" class="admin_icone"/>'
                . '<div class="admin_titre">'
                . '<h5 class="open' . ($section->getCount() ? ' action_a_faire' : '') . '">'
                . $this->renderLabel($section) . '</h5></div>'
                . '<div class="admin_content"><ul>';

            foreach ($section->getChildren() as $link) {
                if (!$link->isDisplayed()) {
                    continue;
                }
                $html .= '<li>' .
                    '<a href="' . $link->getUri() . '" '
                    . ($link->getCount() ? ' class="action_a_faire"' : '') . '>'
                    . htmlspecialchars($link->getLabel())
                    . '</a></li>';
            }

            $html .= '</ul></div></div>';
            $i++;
        }

        $html .= '</td></tr></tbody></table>';

        return $html;
    }

    protected function alter(MenuItem $item)
    {
        $count = 0;
        $displayed = false;
        foreach ($item->getChildren() as $link) {
            if ($link->isDisplayed()) {
                $count += $link->getCount();
                $displayed = true;
            }
        }
        if (!$displayed) {
            $item->setDisplay(false);
        } else {
            $item->setCount($count);
        }
    }

    protected function renderLabel(MenuItem $item)
    {
        return htmlspecialchars($item->getLabel() . ($item->getCount() ? ' (' . $item->getCount() . ')' : ''));
    }
}