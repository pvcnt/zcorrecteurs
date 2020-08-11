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

/**
 * Requêtes sur la table des tentatives de connexion ratées.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TentativeTable extends Doctrine_Table
{
	public function deleteByUserIdAndIp($userId, $ip)
	{
		$this->createQuery()
			->delete()
			->where('user = ?', $userId)
			->andWhere('ip = ?', $ip)
			->execute();
	}
	
	public function countByIp($ip)
	{
		$this->createQuery()
			->where('ip = ?', $ip)
			->count();
	}
	
	public function getByBlocked($hydrationMode = null)
	{
		return $this->getByBlockedQuery()
			->execute(array(), $hydrationMode);
	}
	
	public function getByBlockedQuery()
	{
		return $this->createQuery('t')
			->select('t.ip, t.date, u.id, u.pseudo, g.id, g.nom, g.class')
			->leftJoin('t.Utilisateur u')
			->leftJoin('u.Groupe g')
			->where('t.blocage = 1');
	}

	public function purge()
	{
		return $this->createQuery()
			->delete()
			->where('blocage = 0')
			->execute();
	}
}