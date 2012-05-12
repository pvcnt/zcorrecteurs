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

namespace Zco\Bundle\CoreBundle\Cache;

/**
 * Implémentation de base d'un cache émulant les différentes fonctionnalités 
 * à partir d'opérations de base (lire, écrire, supprimer).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class AbstractCache implements CacheInterface
{
	const INDEX_ID = '__index';
	
	protected $index = array();
	protected $defaultLifetime;
	
	/**
	 * Constructeur.
	 *
	 * @param $defaultLifetime integer Durée de vie par défaut des caches
	 */
	public function __construct($defaultLifetime)
	{
		$this->defaultLifetime = $defaultLifetime;
		$this->readIndex();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function has($id)
	{
		return 
			$this->exists($id) &&
			(
				$this->index[$id]['lifetime'] <= 0 || 
				(time() < $this->index[$id]['time'] + $this->index[$id]['lifetime'])
			);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function add($id, $data, $lifetime = null)
	{
		if ($this->has($id))
		{
			return false;
		}
		
		$this->set($id, $data, $lifetime);
		
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function replace($id, $data, $lifetime = null)
	{
		if (!$this->has($id))
		{
			return false;
		}
		
		$this->set($id, $data, $lifetime);
		
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($id)
	{
		if (strpos($id, '*') !== false)
		{
			$str = '/^'.str_replace('\*', '.*', preg_quote($id, '/')).'$/';
			foreach (array_keys($this->index) as $key)
			{
				if (preg_match($str, $key))
				{
					$this->remove($key);
					unset($this->index[$key]);
				}
			}
			$this->writeIndex();
		}
		else
		{
			$this->remove($id);
			unset($this->index[$id]);
			$this->writeIndex();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get($id, $default = false)
	{
		if (!$this->has($id))
		{
			$this->delete($id);
			
			return $default;
		}
		
		return $this->read($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($id, $data, $lifetime = null)
	{
		$lifetime = ($lifetime === null) ? $this->defaultLifetime : $lifetime;
		
		$this->index[$id] = array(
			'lifetime' => $lifetime, 
			'time' => time(),
		);
		
		$this->write($id, $data, $lifetime);
		$this->writeIndex();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function flush()
	{
		$this->delete('*');
	}

	/**
	 * {@inheritdoc}
	 */
	public function increment($id, $step = 1)
	{
		if ($this->has($id))
		{
			$value    = ((int) $this->get($id)) + $step;
			$lifetime = $this->index[$id]['lifetime'];
		}
		else
		{
			$value    = $step;
			$lifetime = null;
		}
		$this->set($id, $value, $lifetime);
		
		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function decrement($id, $step = 1)
	{
		return $this->increment($id, -$step);
	}
	
	abstract protected function read($id);
	
	abstract protected function write($id, $data, $lifetime);
	
	abstract protected function remove($id);
	
	abstract protected function readIndex();
	
	abstract protected function writeIndex();
	
	protected function exists($id)
	{
		return isset($this->index[$id]);
	}
}
