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
 * Liste les 3 dernières dictées.
 *
 * @return Doctrine_Collection	Les dictées.
*/
function DicteesAccueil()
{
	if(!$d = Container::getService('zco_core.cache')->Get('dictees_accueil'))
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
		Container::getService('zco_core.cache')->Set('dictees_accueil', $d, 120);
	}
	return $d;
}

/**
 * Liste les 3 dictées les plus jouées.
 *
 * @return Doctrine_Collection	Les dictées.
*/
function DicteesLesPlusJouees()
{
	if(!$d = Container::getService('zco_core.cache')->Get('dictees_plusJouees'))
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
		Container::getService('zco_core.cache')->Set('dictees_plusJouees', $d, 3600);
	}
	return $d;
}


/**
 * Choisit une dictée au hasard.
 *
 * @return Dictee	La dictée choisie.
*/
function DicteeHasard()
{
	if(!$d = Container::getService('zco_core.cache')->Get('dictees_hasard'))
	{
		$d = Doctrine_Query::create()
			->from('Dictee')
			->where('etat = ?', DICTEE_VALIDEE)
			->orderBy('RAND()')
			->limit(1)
			->fetchOne();
		Container::getService('zco_core.cache')->Set('dictees_hasard', $d ?: false, 120);
	}
	return $d;
}
