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

namespace Zco\Bundle\AdminBundle\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\Renderer;

/**
 * Moteur de rendu permettant d'afficher l'accueil de l'administration.
 * La majorité des options habituellement disponibles sur un MenuItem
 * ne sont pas supportées pour se concentrer uniquement sur l'affichage
 * de cette page spécifique.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AdminRenderer extends Renderer
{
    /**
     * {@inheritdoc}
     */
    public function render(ItemInterface $item, array $options = array())
    {
        if (!$item->hasChildren() || !$item->getDisplayChildren()) {
            return '';
        }

        $html = '<table border="0" cellspacing="4" cellpadding="0" width="100%"><tbody><tr><td>';
        $perColumn = ceil(count($item) / 2);
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

                $html .= '<li' . ($link->isSeparator() ? ' class="admin_sep"' : '') . '>' .
                    '<a href="' . $link->getUri() . '"'
                    . ($link->getCount() ? ' class="action_a_faire"' : '') . '>'
                    . $this->escape($link->getLabel())
                    . '</a></li>';
            }

            $html .= '</ul></div></div>' . "\n";
            $i++;
        }

        $html .= '</td></tr></tbody></table>';

        return $html;
    }

    protected function alter(ItemInterface $item)
    {
        $count = 0;
        $displayed = false;
        $item->reorderChildren(null);

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

    /**
     * Effectue le rendu d'un label avec son nombre de tâches associées.
     *
     * @param  ItemInterface $item
     * @return string
     */
    protected function renderLabel(ItemInterface $item)
    {
        return $this->escape($item->getLabel() . ($item->getCount() ? ' (' . $item->getCount() . ')' : ''));
    }
}