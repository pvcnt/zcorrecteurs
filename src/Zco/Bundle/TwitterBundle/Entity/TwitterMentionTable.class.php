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
 * Actions sur la table contenant les mentions.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class TwitterMentionTable extends Doctrine_Table
{
	public function getByAccount(\TwitterCompte $account = null)
	{
		return ($query = $this->getByAccountQuery($account)) ? $query->execute() : array();
	}
	
	/**
	 * Renvoie toutes les mentions liées à un compte.
	 *
	 * @param  integer|null $account L'identifiant du compte concerné
	 * @return boolean|\Doctrine_Query
	 */
	public function getByAccountQuery(\TwitterCompte $account = null)
	{
		if (!$account)
		{
			$account = Doctrine_Core::getTable('TwitterCompte')->getDefaultAccount();
		}

		$this->retrieveByAccount($account);

		return Doctrine_Query::create()
			->from('TwitterMention')
			->where('compte_id = ?', $account['id'])
			->orderBy('creation DESC');
	}
	
	public function retrieveByAccount(\TwitterCompte $account = null)
	{
		if (!$account)
		{
			$account = Doctrine_Core::getTable('TwitterCompte')->getDefaultAccount();
		}

		$key = array($account['access_key'], (string) $account['access_secret']);
		$OAuth = \Container::getService('zco_twitter.twitter');
		$OAuth->setTokens($key);

		$derniereMention = Doctrine_Query::create()
			->select('MAX(id)')
			->from('TwitterMention')
			->where('compte_id = ?', $account['id'])
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

		$info = json_decode($OAuth->getMentions($derniereMention));
		if (isset($info->error))
		{
			trigger_error('Impossible de récupérer les mentions', E_USER_WARNING);
			
			return false;
		}
		
		foreach ($info as $inf)
		{
			$mention = new TwitterMention();
			$mention['id']        = $inf->id;
			$mention['compte_id'] = $account['id'];
			$mention['creation']  = date('Y-m-d H:i:s', strtotime($inf->created_at));
			$mention['pseudo']    = $inf->user->screen_name;
			$mention['avatar']    = $inf->user->profile_image_url;
			$mention['nom']       = $inf->user->name;
			$mention['texte']     = str_replace(
				array('&gt;', '&lt;', '&quot;', '&amp;'),
				array('>', '<', '"', '&'),
				$inf->text
		  	);
			$mention->save();
			\Container::getService('zco_admin.manager')->increment('mentions');
		}
		
		return true;
	}

	/**
	 * Marque toutes les mentions liées à un compte comme lues.
	 *
	 * @param  integer|null $account L'identifiant du compte concerné
	 */
	public function setReadByAccount(\TwitterCompte $account = null)
	{
		if (!$account)
		{
			$account = Doctrine_Core::getTable('TwitterCompte')->getDefaultAccount();
		}
		
		Doctrine_Query::create()
			->update('TwitterMention')
			->set('nouvelle', 0)
			->where('compte_id = ?', $account['id'])
			->andWhere('nouvelle = 1')
			->execute();

		\Container::getService('zco_admin.manager')->get('mentions', true);
	}

	/**
	 * Supprime toutes les mentions liées à un compte.
	 *
	 * @param \TwitterCompte Compte à supprimer
	 */
	public function deleteByAccount(\TwitterCompte $account)
	{
		$this->createQuery('m')
			->delete()
			->where('m.compte_id = ?', $account['id'])
			->execute();
	}
}
