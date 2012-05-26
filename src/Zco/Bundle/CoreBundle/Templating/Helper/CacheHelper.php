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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Zco\Bundle\CoreBundle\Cache\CacheInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CacheHelper extends Helper
{
	private $cache;
	
	public function __construct(CacheInterface $cache)
	{
		$this->cache = $cache;
	}
	
	public function get($id, $default = false)
	{
		return $this->cache->get($id, $default);
	}
	
	public function increment($id, $step = 1)
	{
		$this->cache->increment($id, $step);
	}
	
	public function decrement($id, $step = 1)
	{
		$this->cache->decrement($id, $step);
	}
	
	public function output($id, $lifetime = null)
	{
		$this->id = $id;
		$this->lifetime = $lifetime;
		if (($content = $this->cache->get($id)) === false)
		{
			ob_start();
			ob_implicit_flush(false);
			
			return false;
		}
		
		echo $content;
		
		return true;
	}
	
	public function end()
	{
		$this->cache->set($this->id, ob_get_flush(), $this->lifetime);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'cache';
	}
}
