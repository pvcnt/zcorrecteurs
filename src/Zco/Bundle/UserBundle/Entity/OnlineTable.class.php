<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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
 * Requêtes sur la table des connectés.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class OnlineTable extends Doctrine_Table
{
	/**
	 * Met à jour la position de l'utilisateur sur le site.
	 *
	 * @param integer $userId L'identifiant de l'utilisateur
	 * @param string $action L'action courante
	 * @param integer $identifier L'identifiant associé à l'action courante
	 */
	public function updateUserPosition($userId, $action, $identifier = 0)
	{
		$this->createQuery()
			->update()
			->set('action', '?', $action)
			->set('action_identifier', '?', $identifier)
			->where('user_id = ?', $userId)
			->execute();
	}
	
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

	/**
	 * Supprime les vieilles entrées de la table des connectés.
	 *
	 * @return integer Nombre de lignes supprimmées
	 */
	public function purge()
	{
		$users = $this->createQuery('u')
			->select('u.*')
			->where('u.last_action < NOW() - INTERVAL '.NOMBRE_MINUTES_CONNECTE.' MINUTE')
			->andWhere('u.user_id IS NOT NULL')
			->execute();
        
        foreach ($users as $user)
        {
        	\Doctrine_Query::create()
        		->update('Utilisateur')
        		->set('date_derniere_visite', '?', $user->getLastActionDate())
        		->where('id = ?', $user->getUserId())
        		->execute();
        }

        $retval = count($users);
        $users->delete();

        return $retval;
	}
}