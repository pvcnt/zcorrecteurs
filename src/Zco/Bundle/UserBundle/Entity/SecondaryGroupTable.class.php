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
 * SecondaryGroupTable
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SecondaryGroupTable extends Doctrine_Table
{
	/**
	 * Retourne la liste des groupes secondaires associés à un utilisateur.
	 * 
	 * @param  integer $userId L'identifiant de l'utilisateur concerné
	 * @return array Liste des identifiants des groupes associés
	 */
	public function getByUserId($userId)
	{
		$rows = $this->createQuery()
			->select('groupe_id')
			->where('utilisateur_id = ?', $userId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$retval = array();
		foreach ($rows as $row)
		{
			$retval[] = (int) $row['groupe_id'];
		}
		
		return $retval;
	}
}