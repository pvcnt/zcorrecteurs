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

namespace Zco\Bundle\CoreBundle\Menu;

use Knp\Menu\MenuFactory as BaseMenuFactory;

/**
 * Fabrique permettant de créer un élément de menu. Ajouter des options 
 * réutilisant les spécificités de notre implémentation du MenuItem.
 *
 * @author vincen1870 <vincent@zcorrecteurs.fr>
 */
class MenuFactory extends BaseMenuFactory
{
    /**
     * {@inheritdoc}
     */
    public function createItem($name, array $options = array())
    {
        $item = new MenuItem($name, $this);

        $options = array_merge(
            array(
                'uri' => null,
                'label' => null,
                'attributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'labelAttributes' => array(),
                'display' => true,
                'displayChildren' => true,
				'html' => null,
				'preHtml' => null,
				'postHtml' => null,
				'weight' => null,
				'count' => null,
				'separator' => false,
				'credentials' => array(),
            ),
            $options
        );

        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
			->setHtml($options['html'])
			->setHtml($options['preHtml'], 'prefix')
			->setHtml($options['postHtml'], 'suffix')
			->setWeight($options['weight'])
			->setCount($options['count'])
			->setSeparator($options['separator'])
			->secure($options['credentials'])
        ;

        return $item;
    }
}