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

/**
 * Requête sur la table des avertissements aux utilisateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserWarningTable extends Doctrine_Table
{
	/**
	 * Récupère les avertissements associés à une utilisateur donné.
	 *
	 * @param  integer $userId Identifiant de l'utilisateur concerné
	 * @param  integer $hydrationMode Mode d'hydratation
	 * @return mixed
	 */
	public function getByUserId($userId, $hydrationMode = null)
	{
		return $this->createQuery('w')
			->select('w.*, a.id, a.pseudo')
			->leftJoin('w.Admin a')
			->where('w.user_id = ?', $userId)
			->orderBy('w.date')
			->execute(array(), $hydrationMode);
	}
}