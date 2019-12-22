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

namespace Zco\Bundle\CoreBundle\Cache;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

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
	protected $logger;
	protected $defaultLifetime;
	
	/**
	 * Constructeur.
	 *
	 * @param $defaultLifetime integer Durée de vie par défaut des caches
	 */
	public function __construct($defaultLifetime, LoggerInterface $logger = null)
	{
		$this->logger = $logger;
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
			if ($this->logger)
			{
				$this->logger->debug(sprintf('Cache miss (%s)', $id));
			}
			$this->delete($id);
			
			return $default;
		}
		
		if ($this->logger)
		{
			$this->logger->debug(sprintf('Cache hit (%s)', $id));
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
