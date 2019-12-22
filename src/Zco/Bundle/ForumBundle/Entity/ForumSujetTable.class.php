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

use Zco\Bundle\FileBundle\Model\GenericEntityTableInterface;
use Zco\Bundle\Doctrine1Bundle\Model\NamedDoctrineTableInterface;

class ForumSujetTable extends Doctrine_Table implements NamedDoctrineTableInterface
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

	public function getName()
	{
		return 'Sujet du forum';
	}
}
