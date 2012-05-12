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
 * Actions sur la table contenant les comptes Twitter.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class TwitterCompteTable extends Doctrine_Table
{
	/**
	 * Retourne le compte Twitter par défaut.
	 *
	 * @return \TwitterCompte
	 */
	public function getDefaultAccount()
	{
		return $this->createQuery('c')
			->select('*')
			->orderBy('par_defaut DESC')
			->limit(1)
			->execute()
			->offsetGet(0);
	}

	/**
	 * Définit le compte Twitter par défaut.
	 *
	 * @param integer $id Identifiant du compte par défaut
	 */
	public function setDefaultAccount($id)
	{
		Doctrine_Query::create()
			->update('TwitterCompte')
			->set('par_defaut', 0)
			->where('par_defaut <> 0')
			->execute();
		
		Doctrine_Query::create()
			->update('TwitterCompte')
			->set('par_defaut', 1)
			->where('id = ?', $id)
			->execute();
	}

	/**
	 * Renvoie la liste de tous les comptes Twitter.
	 *
	 * @param  boolean $compact Ne renvoyer que le strict nécessaire ? Si faux (par défaut) 
	 *         renvoie le nombre de tweets, la date d'ajout et la date du dernier tweet.
	 * @return \Doctrine_Collection 
	 */
	public function getAll($compact = false)
	{
		return $this->getAllQuery($compact)->execute();
	}
	
	/**
	 * Renvoie la requête récuparant la liste de tous les comptes Twitter.
	 *
	 * @param  boolean $compact Ne renvoyer que le strict nécessaire ? Si faux (par défaut) 
	 *         renvoie le nombre de tweets, la date d'ajout et la date du dernier tweet.
	 * @return \Doctrine_Collection 
	 */
	public function getAllQuery($compact = false)
	{
		$query = $this->createQuery('c')
			->select('c.id, c.nom')
			->orderBy('par_defaut DESC, nom');
		
		if (!$compact)
		{
			$query->addSelect('c.par_defaut, c.tweets, c.creation, t.creation, t.id')
				->leftJoin('c.DernierTweet t');
		}
		
		return $query;
	}

	/**
	 * Ajoute un nouveau compte Twitter.
	 *
	 * @return boolean Le compte a-t-il été ajouté avec succès ?
	 */
	public function add()
	{
		$OAuth = \Container::getService('zco_twitter.twitter');
		$info = $OAuth->addApplication();
		if (!$info)
		{
			return false;
		}

		$account = new TwitterCompte();
		foreach (array('nom', 'id', 'access_secret', 'access_key') as $param)
		{
			$account->$param = array_pop($info);
		}
		$account->creation = date('Y-m-d H:i:s');
		$account->dernier_tweet = null;
		$account->replace();
		
		return true;
	}

	/**
	 * Renvoie un compte Twitter.
	 *
	 * @param  integer|null $id
	 * @return \TwitterCompte
	 */
	public function getById($id = null)
	{
		return ($id === null) ? $this->getDefaultAccount() : $this->find($id);
	}
}
