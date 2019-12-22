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
 */
class SondageTable extends Doctrine_Table
{
	public function Lister($admin = false)
	{
		$query = $this->createQuery('s')
			->select('s.*, u.utilisateur_id, u.utilisateur_pseudo, ROUND((SELECT (SUM(q.nb_votes) + SUM(q.nb_blanc)) FROM SondageQuestion q WHERE q.sondage_id = s.id) / s.nb_questions) AS nb_votes')
			->leftJoin('s.Utilisateur u')
			->orderBy('s.date_debut DESC');
		if ($admin == false)
		{
			$query->where('s.ouvert = 1');
		}
		return $query->execute();
	}

	public function find()
	{
		$args = func_get_args();

		$obj = Doctrine_Query::create()
			->select('s.*, q.*')
			->from('Sondage s')
			->leftJoin('s.Questions q')
			->orderBy('q.ordre')
			->where('s.id = ?', $args[0])
			->execute();

		return $obj[0];
	}
	
	/**
	 * Liste simplement les sondages avec leur id et leur nom.
	 *
	 * @return array
	 */
	public function getAllId()
	{
		return $this->createQuery()
			->select('id, nom')
			->where('ouvert = 1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}