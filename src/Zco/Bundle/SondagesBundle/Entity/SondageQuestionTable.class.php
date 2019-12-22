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
class SondageQuestionTable extends Doctrine_Table
{
	public function getAccueil($utilisateur_id, $ip)
	{
		$query = Doctrine_Query::create()
			->select('q.*, s.*, r.*')
			->from('SondageQuestion q')
			->leftJoin('q.Sondage s')
			->leftJoin('q.SondageReponse r ON r.question_id = q.id');
		
		if ($utilisateur_id > 0)
		{
			$query->leftJoin('q.Votes v WITH v.utilisateur_id = '.$utilisateur_id);
			$query->orderBy('(s.date_debut <= NOW() AND s.date_fin IS NULL OR s.date_fin >= NOw()) DESC, v.id IS NULL DESC, s.date_debut DESC, q.ordre');
		}
		elseif (!is_null($ip))
		{
			$query->leftJoin('q.Votes v WITH v.ip = '.ip2long($ip));
			$query->orderBy('(s.date_debut <= NOW() AND s.date_fin IS NULL OR s.date_fin >= NOw()) DESC, v.id IS NULL DESC, s.date_debut DESC, q.ordre');
		}
		else
		{
			$query->orderBy('(s.date_debut <= NOW() AND s.date_fin IS NULL OR s.date_fin >= NOw()) DESC, s.date_debut DESC, q.ordre');
		}
		$query
			->where('s.ouvert = 1')
			->groupBy('q.id')
			->limit(1);
		return $query->fetchOne();
	}
}