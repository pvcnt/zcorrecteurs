<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Zco\Bundle\FileBundle\Entity\GenericEntity;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileUsageTable extends Doctrine_Table
{
    public function getByFile($id)
    {
        $results = $this->createQuery('u')
            ->select('u.*, t.*')
            ->where('u.file_id = ?', $id)
            ->leftJoin('u.Thumbnail t')
            ->execute();

        $models = array();
        foreach ($results as $i => $result) {
            $models[$result['entity_class']][$result['entity_id']][] = $i;
        }

        foreach ($models as $class => $map) {
            foreach ($map as $id => $indexes) {
                foreach ($indexes as $index) {
                    $results[$index]->setEntity(new GenericEntity($id, $class));
                }
            }
        }

        return $results;
    }
}