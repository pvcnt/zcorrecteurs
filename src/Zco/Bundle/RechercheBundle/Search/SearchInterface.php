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

namespace Zco\Bundle\RechercheBundle\Search;

/**
 * Interface pour les moteurs de recherche.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
interface SearchInterface
{
	const MATCH_ALL = 1;
	const MATCH_ANY = 2;
	const MATCH_BOOLEAN = 3;
	const MATCH_PHRASE = 4;

	const SORT_ASC = 1;
	const SORT_DESC = 2;
	const SORT_RELEVANCE = 3;

	function setFilter($key, $value);
	function setRangedFilter($key, $min, $max);
	function setLimits($results, $offset = 0);

	function setMatchMode($mode);
	function orderBy($mode, $key = null);
	function getResults($search);
}
