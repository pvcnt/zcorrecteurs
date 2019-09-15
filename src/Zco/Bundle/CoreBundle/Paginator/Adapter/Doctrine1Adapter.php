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

namespace Zco\Bundle\CoreBundle\Paginator\Adapter;

/**
 * Adaptateur du paginateur pour les requêtes Doctrine.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Doctrine1Adapter implements AdapterInterface
{
	/**
	 * {@inheritdoc}
	 */
    public function supports($objects)
    {
        return ($objects instanceof \Doctrine_Query);
    }
    
	/**
	 * {@inheritdoc}
	 */
    public function count($objects)
    {
        $query = clone $objects;
        
        return $query->count();
    }
    
	/**
	 * {@inheritdoc}
	 */
    public function slice($objects, $offset, $limit)
    {
        $query = clone $objects;
        $query->offset($offset);
        
        if ($limit)
        {
            $query->limit($limit);
        }
        
        return $query->execute();
    }
}