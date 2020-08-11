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

namespace Zco\Bundle\RechercheBundle\Search;

use Sphinx\Client;

/**
 * Driver Sphinx pour la recherche.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Sphinx extends Search
{
    /**
     * @var Client
     */
	private $client;
	private $matchMode, $sortMode, $sortKey;

	protected function configure()
	{
		$this->client = new Client();
		$this->client->setServer(getenv('SPHINX_HOST') ?: 'localhost', 5000);
		$this->client->setConnectTimeout(1);
		$this->client->setMaxQueryTime(5000); // 5 seconds
		$this->client->setArrayResult(true);
		//$this->client->setIndexWeights(array('message_texte' => 100));
		$this->client->setRankingMode(Client::RANK_PROXIMITY_BM25);
	}

	public function setMatchMode($mode)
	{
		$modes = array(
			self::MATCH_ALL => Client::MATCH_ALL,
			self::MATCH_ANY => Client::MATCH_ANY,
			self::MATCH_BOOLEAN => Client::MATCH_BOOLEAN,
			self::MATCH_PHRASE => Client::MATCH_PHRASE
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
			self::SORT_ASC => Client::SORT_ATTR_ASC,
			self::SORT_DESC => Client::SORT_ATTR_DESC,
			self::SORT_RELEVANCE => Client::SORT_RELEVANCE,
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
		$this->client->setMatchMode($this->matchMode);
		$this->client->setSortMode($this->sortMode, ($this->sortKey === null ? '' : $this->sortKey));
		$this->client->setLimits($this->offset, $this->limit);

		foreach ($this->filters as $key => $filters)
		{
			$this->client->SetFilter($key, $filters);
		}

		foreach ($this->rangedFilters as $key => $filters)
		{
			foreach ($filters as $filter)
			{
				$this->client->setFilterRange($key, $filter[0], $filter[1]);
			}
		}

		$r = $this->client->query($search, $this->index);

		if ($this->client->getLastError()) {
            throw new \Exception($this->client->getLastError());
        }

		if (!isset($r['matches']))
		{
			$r['matches'] = array();
		}
		
		return $r;
	}
}
