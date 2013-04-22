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
 */
class PubliciteStatTable extends Doctrine_Table
{
	/**
	 * Récupère les statistiques d'une publicité sur une semaine donnée.
	 * @param integer $id			L'id de la publicitée.
	 * @param integer $jour			Le premier jour de la semaine souhaitée.
	 * @param integer $mois			Le mois.
	 * @param integer $annee		L'année.
	 * @return Doctrine_Collection
	 */
	public function getForWeek($id, $jour, $mois, $annee)
	{
		$dateDebut = sprintf('%s-%s-%s', $annee, $mois, $jour);
		$dateFin = strtotime('+7 days', strtotime($dateDebut));

		$rows = $this->createQuery('s')
			->select('s.nb_clics, s.nb_affichages, s.date, p.id')
			->leftJoin('s.Publicite p')
			->where('s.publicite_id = ?', $id)
			->andWhere('s.date >= ?', $dateDebut)
			->andWhere('s.date < ?', date('Y-m-d', $dateFin))
			->orderBy('s.date')
			->execute();

		$ret = array();
		for ($i = 0 ; $i < 7 ; $i++)
		{
			$ret[date('Y-m-d', strtotime('+'.$i.' days', strtotime($dateDebut)))] = null;
		}
		foreach ($rows as $row)
		{
			$ret[$row['date']] = $row;
		}
		return $ret;
	}

	/**
	 * Retourne les 10 dernières semaines.
	 * @return array
	 */
	public function getWeeks()
	{
		$monday = date('N') == 1 ? time() : strtotime('previous monday');
		$ret = array($monday);
		for ($i = 1 ; $i <= 10 ; $i++)
		{
			$ret[] = strtotime('-'.$i.' weeks', $monday);
		}
		return $ret;
	}
}