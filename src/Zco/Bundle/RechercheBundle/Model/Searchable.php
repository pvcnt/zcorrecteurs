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

namespace Zco\Bundle\RechercheBundle\Model;

/**
 * Classe de base pour les modèles utilisés par la recherche.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

abstract class Searchable implements SearchableInterface
{
	const CATEGORY_FIELD = 'categorie_id';
	const USERID_FIELD = 'utilisateur_id';

	protected $engine = 'Zco\Bundle\RechercheBundle\Search\Sphinx';
	protected $index = null;
	protected $resultsPerPage = 20;
	protected $result = array();

	private $searcher = null;
	private $categories = array();

	public function __construct()
	{
		$className = $this->engine;
		
		if (!class_exists($className))
		{
			throw new \InvalidArgumentException(sprintf(
				'Search engine %s does not exist.',
				$this->engine));
		}

		$this->searcher = new $className($this->index);
	}

	public function getSearcher()
	{
		return $this->searcher;
	}

	public function setCategories($cats)
	{
		if (is_array($cats))
		{
			foreach ($cats as $cat)
			{
				$this->setCategories($cat);
			}
		}
		else
		{
			$children = ListerEnfants($cats, true);
			foreach ($children as $child)
			{
				$this->categories[] = $child['cat_id'];
			}
		}
		return $this;
	}

	public function setUser($pseudo)
	{
		$mid = getUtilisateurID($pseudo);
		if ($mid === false)
		{
			$mid = -1;
		}

		$this->searcher->setFilter(self::USERID_FIELD, (int)$mid);
		return $this;
	}

	public function setPage($page, $results = null)
	{
		if ($results === null)
		{
			$results = $this->resultsPerPage;
		}
		$offset = ($page - 1) * $results;
		$this->searcher->setLimits($results, $offset);
		return $this;
	}

	public function getResults($query, $checkCredentials = true)
	{
		// Restriction des catégories de recherche à celles autorisées
		static $authorizedCats = null;

		if ($checkCredentials)
		{
			if ($authorizedCats === null)
			{
				$authorizedCats = array();
				foreach (ListerCategories(true) as $cat)
				{
					$authorizedCats[] = $cat['cat_id'];
				}
			}

			if ($this->categories === array())
				$this->categories = $authorizedCats;
		}

		$this->categories = array_unique($this->categories);
		$ok = ($checkCredentials ? 0 : 1);

		foreach ($this->categories as $cat)
		{
			if (!$checkCredentials || in_array($cat, $authorizedCats))
			{
				$ok++;
				$this->searcher->setFilter(self::CATEGORY_FIELD, $cat);
			}
		}

		$this->result = ($ok === 0)
			? array('total' => 0, 'matches' => array())
			: $this->searcher->getResults($query);
		return $this->result;
	}

	public function countResults()
	{
		if (isset($this->result['total']))
			return $this->result['total'];
		return 0;
	}

	public function idsArray($m)
	{
		$m = $m['matches'];
		$ids = array();
		foreach ($m as $match)
		{
			$ids[] = $match['id'];
		}

		return $ids;
	}
}

