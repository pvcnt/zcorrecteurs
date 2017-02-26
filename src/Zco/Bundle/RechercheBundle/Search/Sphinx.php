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

namespace Zco\Bundle\RechercheBundle\Search;

/**
 * Driver Sphinx pour la recherche.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

class Sphinx extends Search
{
	private $client;
	private $matchMode, $sortMode, $sortKey;

	protected function configure()
	{
		$this->client = new \SphinxClient();
		$this->client->SetServer(BASEPATH.'/data/store/sphinx/run/searchd.sock', null);
		$this->client->SetConnectTimeout(1);
		$this->client->SetMaxQueryTime(5000); // 5 seconds
		$this->client->SetArrayResult(true);
		//$this->client->SetIndexWeights(array('message_texte' => 100));
		$this->client->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
	}

	public function setMatchMode($mode)
	{
		$modes = array(
			self::MATCH_ALL => SPH_MATCH_ALL,
			self::MATCH_ANY => SPH_MATCH_ANY,
			self::MATCH_BOOLEAN => SPH_MATCH_BOOLEAN,
			self::MATCH_PHRASE => SPH_MATCH_PHRASE
		);

		if (!isset($modes[$mode]))
		{
			throw new \InvalidArgumentException(sprintf(
				'Match mode %s is not supported',
				$mode));
		}

		$this->matchMode = $modes[$mode];
		return $this;
	}

	public function orderBy($mode, $key = null)
	{
		if (($mode === self::SORT_ASC || $mode === self::SORT_DESC) &&
		    $key === null)
		{
			$modeH = $mode === self::SORT_ASC ? 'SORT_ASC' : 'SORT_DESC';
			throw new \BadMethodCallException(sprintf(
				'Sorting mode %s requires a key to be given',
				$modeH));
		}

		$modes = array(
			self::SORT_ASC => SPH_SORT_ATTR_ASC,
			self::SORT_DESC => SPH_SORT_ATTR_DESC,
			self::SORT_RELEVANCE => SPH_SORT_RELEVANCE
		);

		if (!isset($modes[$mode]))
		{
			throw new \InvalidArgumentException(sprintf(
				'Sort mode %s is not supported',
				$mode));
		}

		$this->sortMode = $modes[$mode];
		$this->sortKey = $key;
		return $this;
	}

	public function getResults($search)
	{
		$this->client->SetMatchMode($this->matchMode);
		$this->client->SetSortMode($this->sortMode,
			($this->sortKey === null ? '' : $this->sortKey));
		$this->client->SetLimits($this->offset, $this->limit);

		foreach ($this->filters as $key => $filters)
		{
			$this->client->SetFilter($key, $filters);
		}

		foreach ($this->rangedFilters as $key => $filters)
		{
			foreach ($filters as $filter)
			{
				$this->client->SetFilterRange($key, $filter[0], $filter[1]);
			}
		}

		$r = $this->client->Query($search, $this->index);

		if ($this->client->_error != '')
			throw new \Exception($this->client->_error);

		if (!isset($r['matches']))
		{
			$r['matches'] = array();
		}
		
		return $r;
	}
}
