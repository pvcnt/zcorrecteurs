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
 */
class PubliciteCampagneTable extends Doctrine_Table
{
	/**
	 * Récupère une liste de campagnes publicitaires.
	 *
	 * @param integer|null $utilisateur_id		L'id d'un utilisateur.
	 * @param array $etat						Liste des états permis.
	 * @return Doctrine_Collection
	 */
	public function listAll($utilisateur_id = null, $etat = array())
	{
		$query = $this->createQuery('c')
			->select('c.*, p.*, u.utilisateur_id, u.utilisateur_pseudo')
			->leftJoin('c.Utilisateur u')
			->leftJoin('c.Publicites p')
			->orderBy('c.etat = \'en_cours\' DESC, c.etat = \'pause\' DESC');
		if (!empty($etat))
		{
			$query->andWhereIn('c.etat', $etat);
		}
		if ($utilisateur_id != null)
		{
			$query->andWhere('c.utilisateur_id = ?', $utilisateur_id);
		}
		return $query->execute();
	}
}