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
 * Requêtes sur la table des sauvegardes de zForm.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZformBackupTable extends Doctrine_Table
{
	/**
	 * Supprime toutes les sauvegardes de plus d'un jour.
	 */
	public function purge()
	{
		$this->createQuery()
			->delete()
			->where('date <= NOW() - INTERVAL 1 DAY')
			->execute();
	}

	/**
	 * Récupère toutes les sauvegardes d'un utilisateur.
	 *
	 * @param  integer $userId L'id de l'utilisateur
	 * @return \Doctrine_Collection
	 */
	public function getByUserId($userId)
	{
		return $this->createQuery()
			->select('*')
			->where('user_id = ?', $userId)
			->orderBy('date DESC')
			->execute();
	}
}