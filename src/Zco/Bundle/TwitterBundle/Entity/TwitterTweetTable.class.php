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
 * Actions sur la table contenant les tweets.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class TwitterTweetTable extends Doctrine_Table
{
	/**
	 * Récupère la liste des tweets liés à un compte donné.
	 *
	 * @param  integer|null $account Identifiant du compte, null pour tous
	 * @return \Doctrine_Collection
	 */
	public function getByAccount($account = null)
	{
		return $this->getByAccountQuery($account)->execute();
	}

	/**
	 * Renvoie la requête récupérant la liste des tweets liés à un compte donné.
	 *
	 * @param  integer|null $account Identifiant du compte, null pour tous
	 * @return \Doctrine_Query
	 */
	public function getByAccountQuery($account = null)
	{
		$query = $this->createQuery('t')
			->select('t.twitter_id, t.creation, t.texte, '
				.'u.utilisateur_id, u.utilisateur_pseudo, '
				.'u.utilisateur_avatar, g.groupe_class, c.nom AS nom_compte')
			->leftJoin('t.Compte c')
			->leftJoin('t.Utilisateur u')
			->leftJoin('u.Groupe g')
			->orderBy('id DESC');

		if ($account !== null)
		{
			$query->where('compte_id = ?', $account);
		}

		return $query;
	}

	/**
	 * Renvoie la liste des tweets dont l'identifiant est dans une liste.
	 *
	 * @param  array $ids Liste des identifiants des tweets à renvoyer
	 * @return array
	 */
	public function getByIds(array $ids)
	{
		return !empty($ids) ? $this->createQuery('t')
			->select('t.twitter_id, t.creation, t.texte, '
				.'u.utilisateur_id, u.utilisateur_pseudo, '
				.'u.utilisateur_avatar, g.groupe_class, c.nom AS nom_compte')
			->leftJoin('t.Compte c')
			->leftJoin('t.Utilisateur u')
			->leftJoin('u.Groupe g')
			->whereIn('t.id', $ids)
			->orderBy('id DESC')
			->fetchArray() : array();
	}

	/**
	 * Ajoute un nouveau tweet.
	 *
	 * @param  string $texte Contenu du tweet à envoyer
	 * @param  integer $auteurId Identifiant de l'auteur du tweet.
	 * @param  \TwitterCompte|null $compte Compte où poster le tweet
	 * @param  \TwitterMention|null $mention Réponse à une mention
	 * @return boolean Le tweet a-t-il bien été ajouté ?
	 */
	public function add($texte, $auteurID, \TwitterCompte $compte = null, \TwitterMention $mention = null)
	{
		if ($compte === null)
		{
			$compte = Doctrine_Core::getTable('TwitterCompte')->getDefaultAccount();
		}
		
		$key = array($compte['access_key'], $compte['access_secret']);

		$OAuth = \Container::getService('zco_twitter.twitter');
		$OAuth->setTokens($key);
		$info = $OAuth->addTweet($texte, ($mention === null) ? null : $mention['id']);
		$info = json_decode($info);

		$tweetID = isset($info->id) ? $info->id : false;
		if (!$tweetID)
		{
			return false;
		}

		if ($mention === null) // Ne pas enregistrer les réponses
		{
			$Tweet = new TwitterTweet();
			$Tweet->twitter_id = $tweetID;
			$Tweet->compte_id = $compte['id'];
			$Tweet->utilisateur_id = $auteurID;
			$Tweet->creation = date('Y-m-d H:i:s');
			$Tweet->texte = $texte;
			$Tweet->save();
			
			$compte->tweets++;
			$compte->dernier_tweet = $tweetID;
			$compte->save();
		}
		else // Mais se souvenir du lien sur Twitter
		{
			$mention->reponse_id = $tweetID;
			$mention->save();
		}

		Container::getService('zco_core.cache')->delete('accueil_derniersTweets');
		
		return true;
	}

	/**
	 * Supprime tous les tweets liés à un compte.
	 *
	 * @param \TwitterCompte Compte à supprimer
	 */
	public function deleteByAccount(\TwitterCompte $compte)
	{
		$this->createQuery('t')
			->delete()
			->where('t.compte_id = ?', $compte->id)
			->execute();
	}
}
