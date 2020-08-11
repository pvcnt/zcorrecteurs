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
 * Requêtes sur la table des préférences utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserPreferenceTable extends Doctrine_Table
{
	/**
	 * Récupère les préférences par défaut.
	 *
	 * @return UserPreference
	 */
	public function getDefault()
	{
		return $this->getById(0);
	}

	/**
	 * Récupère les préférences d'un utilisateur donné.
	 *
	 * @param  integer $id L'identifiant de l'utilisateur
	 * @return UserPreference
	 */
	public function getById($id)
	{
		return $this->createQuery()
			->select('*')
			->where('user_id = ?', $id)
			->fetchOne();
	}
}