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

namespace Zco\Bundle\Doctrine1Bundle\Adapter;

use \PDOStatement as BasePDOStatement;
use \PDO;

/**
 * Extension de PDOStatement pour enregistrer les requêtes effectuées.
 * 
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 */
class PDOStatement extends BasePDOStatement
{
	private $pdo;
	private $query;
	private $time = 0;
	private $bind = array();

	/**
	 * La visibilité non publique est demandée par PDO.
	 * {@inheritdoc}
	 */
	private function __construct($pdo)
	{
		//Ancien code, risque de casser la compatibilité lorsqu'injecté dans Doctrine.
		//$this->setFetchMode(PDO::FETCH_ASSOC);
		
		$this->pdo = $pdo;
		$this->query = $this->pdo->temp;
	}

	/**
	 * {@inheritdoc}
	 */
	public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
	{
		$now = microtime(true);
		$retval = parent::bindColumn($column, $param, $type, $maxlen, $driverdata);
		$this->time += microtime(true) - $now;
		
		return $retval;
	}

	/**
	 * {@inheritdoc}
	 */
	public function bindParam($parameter, &$variable, $dataType = PDO::PARAM_STR, $length = null, $options = null)
	{
		$this->bind[$parameter] = &$variable;
		
		return parent::bindParam($parameter, $variable, $dataType, $length, $options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function bindValue($parameter, $value, $dataType = PDO::PARAM_STR)
	{
		$this->bind[$parameter] = $value;
		
		return parent::bindValue($parameter, $value, $dataType);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute($parameters = null)
	{
		$now = microtime(true);
		$retval = parent::execute($parameters);
		$this->time += microtime(true) - $now;
		
		if ($parameters)
		{
			$this->bind = array_merge($this->bind, $parameters);
		}
		$this->pdo->addQuery($this->query, $this->time, $this->bind);
		$this->time = 0;
		
		return $retval;
	}
}
