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

namespace Zco\Bundle\SearchBundle\Search;

/**
 * Couche d'abstraction pour la recherche.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
abstract class Search implements SearchInterface
{
	protected $index;
	protected $limit, $offset;
	protected $filters, $rangedFilters;

	public function __construct($index)
	{
		$this->index = $index;

		$this->setMatchMode(self::MATCH_ALL);
		$this->setLimits(50, 0);
		$this->orderBy(self::SORT_RELEVANCE);

		$this->filters = array();
		$this->rangedFilters = array();

		$this->configure();
	}

	protected function configure()
	{
	}

	public function setFilter($key, $value)
	{
		if (!isset($this->filters[$key]))
		{
			$this->filters[$key] = array();
		}

		$this->filters[$key][] = (int) $value;
		
		return $this;
	}

	public function setRangedFilter($key, $min, $max)
	{
		if (!isset($this->rangedFilters[$key]))
		{
			$this->rangedFilters[$key] = array();
		}

		$this->rangedFilters[$key][] = array((int) $min, (int) $max);
		
		return $this;
	}

	public function setLimits($results, $offset = 0)
	{
		if ($results <= 0)
		{
			throw new \InvalidArgumentException(sprintf(
				'The number of results must be greater than 0'
				.' (got %s)',
				$results));
		}
		
		if ($offset < 0)
		{
			throw new \InvalidArgumentException(sprintf(
				'The offset has to be a positive integer'
				.' (got %s)',
				$offset));
		}

		$this->limit = (int) $results;
		$this->offset = (int) $offset;
		
		return $this;
	}
}
