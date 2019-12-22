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

	/**
	 * Met à jour les sanctions données aux membres.
	 */
	public function purge()
	{
		//Décrémente le nombre de jours restants sur les sanctions en cours.
		$this->createQuery('p')
			->update()
			->set('remaining_duration', 'remaining_duration - 1')
			->where('finished = ?', false)
			->andWhere('remaining_duration > 0')
			->execute();
		
		//Arrêt des sanctions trop vieilles.
		$punishments = $this->createQuery()
			->select('*')
			->where('duration > 0')
			->andWhere('remaining_duration = 0')
			->execute();

		foreach ($punishments as $punishment)
		{
			$punishment->complete();
			$punishment->getUser()->unapplyPunishment($punishment);
		}
	}
}