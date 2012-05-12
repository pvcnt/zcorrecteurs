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

namespace Zco\Bundle\Doctrine1Bundle\Adapter;

use \PDO;

/**
 * Extension de PDO pour pouvoir enregistrer les requêtes effectuées 
 * et le temps d'exécution.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 */
class PDOAdapter extends PDO
{
	private $queries = array();
	private $debug = false;
	private $connexionTime;
	private $dsn;
	private $username;
	private $password;
	
	public $temp;
	
	/**
	 * {@inheritdoc}
	 */
	public function __construct($dsn, $username, $password, $options = array())
	{
		$this->dsn      = $dsn;
		$this->username = $username;
		$this->password = $password;
		
		$now = microtime(true);
		parent::__construct($dsn, $username, $password, $options);
		$this->connexionTime = microtime(true) - $now;
		
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('\Zco\Bundle\Doctrine1Bundle\Adapter\PDOStatement', array($this)));
		$this->setAttribute(PDO::ATTR_PERSISTENT, false);
		
		// http://wezfurlong.org/blog/2006/apr/using-pdo-mysql/
		$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
	}
	
	/**
	 * Active ou désactive le mode de débogage. L'enregistrement des requêtes 
	 * effectuées ne fonctionnera que si ce mode est activé.
	 *
	 * @param boolean $debug
	 */
	public function setDebug($debug)
	{
		$this->debug = (boolean) $debug;
	}
	
	/**
	 * Vérifie si le mode de débogage est activé.
	 * 
	 * @return boolean
	 */
	public function isDebug()
	{
		return $this->debug;
	}

	/**
	 * {@inheritdoc}
	 */
	public function exec($statement)
	{
		$now  = microtime(true);
		$stmt = parent::exec($statement);
		
		$this->addQuery($statement, microtime(true) - $now);
		
		return $stmt;
	}

	/**
	 * {@inheritdoc}
	 */
	public function query($statement)
	{
		$now = microtime(true);
		$stmt = call_user_func_array(array($this, 'parent::query'), func_get_args());
		if (false !== $stmt)
		{
			$this->addQuery($statement, microtime(true) - $now);
			
			return $stmt;
		}
		
		throw new \RuntimeException('Error while invoking PDO::query(), maybe wrong arguments.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare($statement, $options = array())
	{
		$this->temp = $statement;
		
		return parent::prepare($statement, $options);
	}

	/**
	 * Ajoute une requête aux logs.
	 * 
	 * @param string $sql Requête SQL
	 * @param float $time Temps d'exécution
	 * @param array $params Paramètres de la requête
	 */
	public function addQuery($sql, $time, array $params = array())
	{
		if (!$this->debug)
		{
			return;
		}
		
		$array = array('sql' => $sql, 'time' => $time, 'params' => $params);
		$dbt = debug_backtrace();
		$dbt = $dbt[1];
		if ($dbt['file'] && $dbt['line'])
		{
			$array['file'] = str_replace(BASEPATH, '~', $dbt['file']);
			$array['line'] = $dbt['line'];
		}
		
		$this->queries[] = $array;
	}

	/**
	 * Retourne le temps de connexion à la base de données.
	 *
	 * @return float
	 */
	public function getConnexionTime()
	{
		return $this->connexionTime;
	}

	/**
	 * Retourne la liste des requêtes enregistrées.
	 *
	 * @return array
	 */
	public function getQueries()
	{
		return $this->queries;
	}
	
	/**
	 * Retourne la chaîne de connexion utilisée pour se connecter.
	 *
	 * @return string
	 */
	public function getDsn()
	{
		return $this->dsn;
	}
	
	/**
	 * Retourne le nom de l'utilisateur utilisé pour se connecter.
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}
	
	/**
	 * Retourne le mot de passe de l'utilisateur utilisé pour se connecter.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}
}
