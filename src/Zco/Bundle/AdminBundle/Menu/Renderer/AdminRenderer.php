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

        $this->alter($item);

        $html = '<ul class="nav nav-tabs">';
        foreach ($item->getChildren() as $tab) {
            if (!$tab->getDisplayChildren()) {
                continue;
            }

            $html .= '<li><a href="#' . rewrite($tab->getName()) . '" data-toggle="tab">' . $this->renderLabel($tab) . '</a></li>';
        }
        $html .= '</ul>' . "\n\n";

        $html .= '<div class="tab-content">';
        foreach ($item->getChildren() as $i => $tab) {
            if (!$tab->isDisplayed()) {
                continue;
            }

            $html .= '<div class="tab-pane" id="' . rewrite($tab->getName()) . '">';
            $html .= '<table border="0" cellspacing="4" cellpadding="0" width="100%"><tbody><tr><td>';

            $perColumn = ceil(count($tab) / 2);
            $i         = 0;

            foreach ($tab->getChildren() as $section) {
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

            $html .= '</td></tr></tbody></table></div>' . "\n";
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Recalcule les propriétés "display" et "count" pour prendre en compte 
     * la hiérarchie des liens contenus dans des sections contenues dans des 
     * onglets. Les liens n'étant pas affichés sont supprimés afin de 
     * simplifier l'affichage ultérieur.
     *
     * @param ItemInterface $item La racine du menu
     */
    protected function alter(ItemInterface $item)
    {
        $item->reorderChildren(null);

        foreach ($item->getChildren() as $tab) {
            $tabCount          = 0;
            $sectionsDisplayed = false;
            $tab->reorderChildren(null);

            foreach ($tab->getChildren() as $section) {
                $sectionCount   = 0;
                $linksDisplayed = false;
                $section->reorderChildren(null);

                foreach ($section->getChildren() as $link) {
                    if ($link->isDisplayed()) {
                        $sectionCount += $link->getCount();
                        $tabCount += $link->getCount();
                        $linksDisplayed = true;
                    }
                }

                if (!$linksDisplayed) {
                    $tab->removeChild($section);
                } else {
                    $section->setCount($sectionCount);
                    $sectionsDisplayed = true;
                }
            }

            if (!$sectionsDisplayed) {
                $item->removeChild($tab);
            } else {
                $tab->setCount($tabCount);
            }
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