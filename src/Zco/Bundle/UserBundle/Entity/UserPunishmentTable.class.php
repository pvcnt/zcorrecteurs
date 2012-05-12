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

/**
 * Requêtes sur la table des sanctions.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserPunishmentTable extends Doctrine_Table
{
	/**
	 * Récupère une sanction par son identifiant.
	 *
	 * @param  integer $id
	 * @return UserPunishment
	 */
	public function getById($id)
	{
		return $this->createQuery('p')
			->select('p.*, u.id, u.pseudo, a.id, a.pseudo, g.id, g.nom')
			->leftJoin('p.User u')
			->leftJoin('p.Admin a')
			->leftJoin('p.Group g')
			->where('p.id = ?', $id)
			->fetchOne();
	}
	
	/**
	 * Récupère les sanctions associées à une utilisateur donné.
	 *
	 * @param  integer $userId
	 * @param  integer $hydrationMode
	 * @return \Doctrine_Collection
	 */
	public function getByUserId($userId, $hydrationMode = null)
	{
		return $this->createQuery('p')
			->select('p.*, u.id, u.pseudo, a.id, a.pseudo, g.id, g.nom')
			->leftJoin('p.User u')
			->leftJoin('p.Admin a')
			->leftJoin('p.Group g')
			->where('p.user_id = ?', $userId)
			->execute(array(), $hydrationMode);
	}
}