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

namespace Zco\Bundle\CoreBundle\Paginator\Adapter;

/**
 * Adaptateur du paginateur pour des tableaux natifs PHP.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ArrayAdapter implements AdapterInterface
{
	/**
	 * {@inheritdoc}
	 */
    public function supports($objects)
    {
        return is_array($objects);
    }
    
	/**
	 * {@inheritdoc}
	 */
    public function count($objects)
    {
        return count($objects);
    }
    
	/**
	 * {@inheritdoc}
	 */
    public function slice($objects, $offset, $limit)
    {
        return array_slice($objects, $offset, $limit);
    }
}