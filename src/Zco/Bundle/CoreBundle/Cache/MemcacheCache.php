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

namespace Zco\Bundle\CoreBundle\Cache;

/**
 * Implémentation d'un cache stockant ses données en Memcache.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MemcacheCache extends AbstractCache
{
	protected $memcache;
	
	/**
	 * Constructeur.
	 *
	 * @param integer $defaultLifetime Durée de vie par défaut des caches
	 */
	public function __construct($defaultLifetime)
	{
		$this->memcache = new \Memcache;
		$this->memcache->connect('localhost', 11211);
		
		parent::__construct($defaultLifetime);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function flush()
	{
		$this->memcache->flush();
		$this->index = array();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function replace($id, $data, $lifetime = null)
	{
		$lifetime = ($lifetime === null) ? $this->defaultLifetime : $lifetime;
		
		return $this->memcache->replace($id, $data, 0, $lifetime);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function add($id, $data, $lifetime = null)
	{
		$lifetime = ($lifetime === null) ? $this->defaultLifetime : $lifetime;
		
		return $this->memcache->add($id, $data, 0, $lifetime);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function read($id)
	{
		return $this->memcache->get($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write($id, $data, $lifetime)
	{
		$this->memcache->set($id, $data, 0, $lifetime);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function remove($id)
	{
		$this->memcache->delete($id, 0 /*http://www.php.net/manual/fr/memcache.delete.php#95344*/);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function readIndex()
	{
		$this->index = $this->memcache->get(self::INDEX_ID) ?: array();
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function writeIndex()
	{
		$this->memcache->set(self::INDEX_ID, $this->index);
	}
}
