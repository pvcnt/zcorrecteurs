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

namespace Zco\Bundle\CoreBundle;

use Zco\Bundle\CoreBundle\Cache\CacheInterface;

/**
 * Classe permettant de stocker des paires clé/valeur en BDD. Une surcouche 
 * enregistrant les valeurs en cache permet de garantir la légèreté.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Registry
{
	private $cache;
	private $prefix;
	
	/**
	 * Constructeur.
	 *
	 * @param CacheInterface $cache
	 * @param string $prefix Le préfix des tables en base de données
	 */
	public function __construct(CacheInterface $cache, $prefix)
	{
		$this->cache = $cache;
	    $this->prefix = $prefix;
	}

	/**
	 * Enregistre une valeur.
	 *
	 * @param string $key Clé identifiant la valuer
	 * @param mixed $value Valeur à enregistrer
	 */
	public function set($key, $value)
	{
		$dbh   = \Doctrine_Manager::connection()->getDbh();
		$query = $dbh->prepare('REPLACE INTO '.$this->prefix.'registry VALUES(:key, :value)');
		$query->bindParam(':key', $key);
		$query->bindValue(':value', serialize($value));
		$query->execute();
		
		$this->cache->set('registry_'.$key, $value, 0);
	}


	/**
	 * Récupère une valeur enregistrée.
	 *
	 * @param  string $key Clé identifiant la valeur
	 * @param  mixed $default Valeur à retourner si l'enregistrement n'existe pas
	 * @return mixed $value Valeur enregistrée
	 */
	public function get($key, $default = false)
	{
		if (($value = $this->cache->get('registry_'.$key)) !== false)
		{
			return $value;
		}
		
		$dbh   = \Doctrine_Manager::connection()->getDbh();
		$query = $dbh->prepare('SELECT registry_value FROM '.$this->prefix.'registry WHERE registry_key = :key');
		$query->bindParam(':key', $key);
		$query->execute();
		$value = $query->fetchColumn();
		if ($value === false)
		{
			return $default;
		}
		$value = unserialize($value);
		$this->cache->set('registry_'.$key, $value, 0);
		
		return $value;
	}


	/**
	 * Supprime une valeur enregistrée
	 *
	 * @param string $key Clé identifiant la valeur
	 */
	public function delete($key)
	{
		$dbh   = \Doctrine_Manager::connection()->getDbh();
		$query = $dbh->prepare('DELETE FROM '.$this->prefix.'registry WHERE registry_key = :key');
		$query->bindParam(':key', $key);
		$query->execute();
		
		$this->cache->delete('registry_'.$key);
	}
}
