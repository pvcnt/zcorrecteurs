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

namespace Zco\Bundle\CoreBundle;

use Doctrine\Common\Cache\Cache;

/**
 * Classe permettant de stocker des paires clé/valeur en BDD. Une surcouche 
 * enregistrant les valeurs en cache permet de garantir la légèreté.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Registry
{
	private $cache;
	
	/**
	 * Constructeur.
	 *
	 * @param Cache $cache
	 */
	public function __construct(Cache $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Enregistre une valeur.
	 *
	 * @param string $key Clé identifiant la valuer
	 * @param mixed $value Valeur à enregistrer
	 */
	public function set($key, $value)
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();
		$query = $dbh->prepare('REPLACE INTO zcov2_registry VALUES(:key, :value)');
		$query->bindParam(':key', $key);
		$query->bindValue(':value', serialize($value));
		$query->execute();
		
		$this->cache->save('registry_'.$key, $value, 0);
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
		if (($value = $this->cache->fetch('registry_'.$key)) !== false)
		{
			return $value;
		}
		
		$dbh   = \Doctrine_Manager::connection()->getDbh();
		$query = $dbh->prepare('SELECT registry_value FROM zcov2_registry WHERE registry_key = :key');
		$query->bindParam(':key', $key);
		$query->execute();
		$value = $query->fetchColumn();
		if ($value === false)
		{
			return $default;
		}
		$value = unserialize($value);
		$this->cache->save('registry_'.$key, $value, 0);
		
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
		$query = $dbh->prepare('DELETE FROM zcov2_registry WHERE registry_key = :key');
		$query->bindParam(':key', $key);
		$query->execute();
		
		$this->cache->delete('registry_'.$key);
	}
}
