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
 * Requêtes sur la table des connectés.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class OnlineTable extends Doctrine_Table
{
	/**
	 * Renvoie la liste de tous les visiteurs connectés.
	 *
	 * @param  boolean $showAnonymousUsers Retourner les anonymes ?
	 * @return \Doctrine_Collection
	 */
	public function getAll($showAnonymousUsers)
	{
		$query = $this->createQuery('m')
			->select('u.id, u.pseudo, u.avatar, m.ip, m.last_action, c.id, c.nom, g.id, g.nom')
			->leftJoin('m.User u')
			->leftJoin('u.Groupe g')
			->leftJoin('m.Category c')
			->where('last_action >= NOW() - INTERVAL ? MINUTE', NOMBRE_MINUTES_CONNECTE)
			->orderBy('m.last_action DESC');
		
		if (!$showAnonymousUsers)
		{
			$query->andWhere('m.user_id IS NOT NULL');
			$query->andWhere('m.user_id > 0');
		}
		
		return $query->execute();
	}
	
	/**
	 * Compte tous les visiteurs connectés.
	 *
	 * @return \Doctrine_Collection
	 */
	public function countAll()
	{
		return $this->createQuery()
			->where('last_action >= NOW() - INTERVAL ? MINUTE', NOMBRE_MINUTES_CONNECTE)
			->count();
	}
	
	/**
	 * Supprime les entrées dans la table des connectés pour l'utilisateur donné.
	 * 
	 * @param integer $userId L'identifiant de l'utilisateur
	 */
	public function deleteByUserId($userId)
	{
		$this->createQuery()
			->delete()
			->where('user_id = ?', $userId)
			->execute();
	}
	
	/**
	 * Supprime les entrées dans la table des connectés pour l'adresse IP donnée.
	 * 
	 * @param integer $ip L'adresse IP
	 */
	public function deleteByIp($ip)
	{
		$this->createQuery()
			->delete()
			->where('ip = ?', $ip)
			->execute();
	}
	
	/**
	 * Retourne le nom du robot d'exploration associé à un user-agent (s'il existe).
	 *
	 * @param  string $userAgent L'user-agent à tester
	 * @return string|null Le nom du robot, null s'il ne s'agit pas d'un robot
	 */
	public function getBotName($userAgent)
	{
		static $useragents;
		
		//Impossible d'initialiser une variable statique avec un include apparemment…
		if (!isset($useragents))
		{
			$useragents = include(__DIR__.'/../modeles/useragents.php');
		}

		if (isset($useragents[$userAgent]))
		{
			return $useragents[$userAgent];
		}
	}
}