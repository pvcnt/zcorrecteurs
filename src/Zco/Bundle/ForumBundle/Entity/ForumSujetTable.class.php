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

use Zco\Bundle\FileBundle\Model\GenericEntityTableInterface;

class ForumSujetTable extends Doctrine_Table implements GenericEntityTableInterface
{
	public function Forum($forum_id)
	{
		$query = Doctrine_Query::create()
			->select('')
			->from('ForumSujet s')
			->leftJoin('s.DernierMessage m')
			->leftJoin('s.Utilisateur u1')
			->leftJoin('m.Utilisateur u2')
			->leftJoin('u1.Groupe g1')
			->leftJoin('u2.Groupe g2');
	}
	
	public function getEntities(array $pks)
    {
        return $this->createQuery('s')
            ->select('s.*')
            ->whereIn('s.id', $pks)
            ->execute();
    }
}
