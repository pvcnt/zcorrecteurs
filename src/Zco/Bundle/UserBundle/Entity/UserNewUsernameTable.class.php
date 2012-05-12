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
 * Requêtes sur la table des demandes de changement de pseudonyme.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserNewUsernameTable extends Doctrine_Table
{
	/**
	 * Vérifie si un utilisateur a déjà une demande de changement de pseudonyme 
	 * en attente.
	 *
	 * @param  integer $userId L'identifiant de l'utilisateur concerné
	 * @return boolean
	 */
	public function hasWaitingQuery($userId)
	{
		return $this->createQuery()
			->where('user_id = ?', $userId)
			->andWhere('status = ?', CH_PSEUDO_ATTENTE)
			->count() > 0;
	}
	
	/**
	 * Récupère une demande par son identifiant
	 *
	 * @param  integer $id
	 * @return UserNewUsername
	 */
	public function getById($id)
	{
		return $this->createQuery('q')
			->select('q.*, u.*, a.*')
			->leftJoin('q.User u')
			->leftJoin('q.Admin a')
			->where('id = ?', $id)
			->fetchOne();
	}
	
	/**
	 * Récupère toutes les demandes en attente de validation.
	 *
	 * @param  integer $hydrationMode Le mode d'hydratation
	 * @return \Doctrine_Collection
	 */
	public function getWaitingQueries($hydrationMode = null)
	{
		return $this->createQuery('q')
			->select('q.*, u.*')
			->leftJoin('q.User u')
			->where('status = ?', CH_PSEUDO_ATTENTE)
			->orderBy('q.changement_date')
			->execute(array(), $hydrationMode);
	}
	
	/**
	 * Récupère toutes les demandes associées à un utilisateur.
	 *
	 * @param  integer $userId L'identifiant de l'utilisateur concerné
	 * @param  integer $hydrationMode Le mode d'hydratation
	 * @return mixed
	 */
	public function getByUserId($userId, $hydrationMode = null)
	{
		return $this->createQuery('q')
			->select('q.*, u.id, u.pseudo, a.id, a.pseudo')
			->leftJoin('q.User u')
			->leftJoin('q.Admin a')
			->where('user_id = ?', $userId)
			->orderBy('q.changement_date')
			->execute(array(), $hydrationMode);
	}
}