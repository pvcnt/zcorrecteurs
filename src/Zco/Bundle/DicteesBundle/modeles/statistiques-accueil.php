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

use Doctrine\Common\Cache\Cache;

/**
 * Liste les 3 dernières dictées.
 *
 * @param Cache $cache
 * @return array
*/
function DicteesAccueil(Cache $cache)
{
	if(!$d = $cache->fetch('dictees_accueil'))
	{
		$dictees = Doctrine_Query::create()
			->from('Dictee')
			->where('etat = ?', DICTEE_VALIDEE)
			->orderBy('creation DESC')
			->limit(3)
			->execute();
			
		$d = array();
		foreach ($dictees as $dictee)
			$d[] = $dictee;
		$cache->save('dictees_accueil', $d, 120);
	}
	return $d;
}

/**
 * Liste les 3 dictées les plus jouées.
 *
 * @param Cache $cache
 * @return array
*/
function DicteesLesPlusJouees(Cache $cache)
{
	if(!$d = $cache->fetch('dictees_plusJouees'))
	{
		$dictees = Doctrine_Query::create()
			->from('Dictee')
			->where('etat = ?', DICTEE_VALIDEE)
			->orderBy('participations DESC')
			->limit(3)
			->execute();
		$d = array();
		foreach ($dictees as $dictee)
			$d[] = $dictee;
		$cache->save('dictees_plusJouees', $d, 3600);
	}
	return $d;
}


/**
 * Choisit une dictée au hasard.
 *
 * @param Cache $cache
 * @return Dictee	La dictée choisie.
*/
function DicteeHasard(Cache $cache)
{
	if(!$d = $cache->fetch('dictees_hasard'))
	{
		$d = Doctrine_Query::create()
			->from('Dictee')
			->where('etat = ?', DICTEE_VALIDEE)
			->orderBy('RAND()')
			->limit(1)
			->fetchOne();
		$cache->save('dictees_hasard', $d ?: false, 120);
	}
	return $d;
}
