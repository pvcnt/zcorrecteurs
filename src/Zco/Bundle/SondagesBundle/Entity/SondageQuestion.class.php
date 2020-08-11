<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
 * SondageQuestion
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class SondageQuestion extends BaseSondageQuestion
{
	/**
	 * Sélectionne l'ordre de la question dans le sondage lors de
	 * l'insertion de sorte à ce qu'elle soit insérée à la fin du sondage.
	 *
	 * @see vendor/doctrine/Doctrine/Doctrine_Record#preInsert($event)
	 */
	public function preInsert($event)
	{
		$this['ordre'] = Doctrine_Query::create()
			->select('MAX(q.ordre)')
			->from('SondageQuestion q')
			->where('q.sondage_id = ?', $this['sondage_id'])
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) + 1;
	}

	/**
	 * Vérifie si un utilisateur et une IP donnée ont déjà voté.
	 *
	 * @param integer $utilisateur_id		Id de l'utilisateur.
	 * @param string $ip					IP de l'utilisateur.
	 * @return boolean
	 */
	public function aVote($utilisateur_id, $ip)
	{
		$query = Doctrine_Query::create()
			->select('COUNT(*)')
			->from('SondageVote')
			->where('question_id = ?', $this['id']);
		if (isset($utilisateur_id) && $utilisateur_id > 0)
		{
			$query->andWhere('utilisateur_id = ?', $utilisateur_id);
		}
		else
		{
			$query->andWhere('ip = ?', ip2long($ip));
		}
		return ($query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) > 0);
	}

	/**
	 * Récupère l'id de la question suivante. Retourne null si jamais
	 * la question est la dernière du sondage.
	 *
	 * @return integer|null
	 */
	public function getQuestionSuivante()
	{
		$id = Doctrine_Query::create()
			->select('id')
			->from('SondageQuestion')
			->orderBy('ordre')
			->where('ordre >= ?', $this['ordre'])
			->andWhere('id <> ?', $this['id'])
			->andWhere('sondage_id = ?', $this['sondage_id'])
			->limit(1)
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
		return !empty($id) ? $id : null;
	}

	/**
	 * Retourne la liste des votes associés à des réponses libres.
	 * @return Doctrine_Collection
	 */
	public function getVotesLibres()
	{
		return Doctrine_Query::create()
			->select('v.*, t.*, u.*')
			->from('SondageVote v')
			->leftJoin('v.TexteLibre t')
			->leftJoin('v.Utilisateur u')
			->where('v.question_id = ?', $this['id'])
			->andWhere('t.vote_id IS NOT NULL')
			->orderBy('v.date DESC')
			->execute();
	}

	public function monter()
	{
		$min = Doctrine_Query::create()
			->select('MIN(ordre)')
			->from('SondageQuestion')
			->where('sondage_id = ?', $this['sondage_id'])
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

		if ($this['ordre'] > $min)
		{
			$autre_question = Doctrine_Query::create()
				->select('ordre, id')
				->from('SondageQuestion')
				->where('sondage_id = ?', $this['sondage_id'])
				->andWhere('ordre <= ?', $this['ordre'])
				->andWhere('id <> ?', $this['id'])
				->orderBy('ordre DESC')
				->limit(1)
				->fetchOne();

			$diff = abs($autre_question['ordre'] - $this['ordre']);
			$this['ordre'] -= $diff;
			$this->save();

			$autre_question['ordre'] += $diff;
			$autre_question->save();
			return true;
		}
		else
		{
			return false;
		}
	}

	public function descendre()
	{
		$max = Doctrine_Query::create()
			->select('MAX(ordre)')
			->from('SondageQuestion')
			->where('sondage_id = ?', $this['sondage_id'])
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

		if ($this['ordre'] < $max)
		{
			$autre_question = Doctrine_Query::create()
				->select('ordre, id')
				->from('SondageQuestion')
				->where('sondage_id = ?', $this['sondage_id'])
				->andWhere('ordre >= ?', $this['ordre'])
				->andWhere('id <> ?', $this['id'])
				->orderBy('ordre ASC')
				->limit(1)
				->fetchOne();

			$diff = abs($autre_question['ordre'] - $this['ordre']);
			$this['ordre'] += $diff;
			$this->save();

			$autre_question['ordre'] -= $diff;
			$autre_question->save();
			return true;
		}
		else
		{
			return false;
		}
	}
}