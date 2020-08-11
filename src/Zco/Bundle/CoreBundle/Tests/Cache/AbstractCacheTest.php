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

namespace Zco\Bundle\CoreBundle\Tests;

use Zco\Bundle\CoreBundle\Cache\FileCache;

abstract class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{	
	protected $cache;
	
	public function testGetSet()
	{
		$this->cache->set('aKey', 'aValue', 10);
		$this->assertEquals(
			'aValue', $this->cache->get('aKey'),
			'Le cache stocke et retrouve les types primitifs'
		);
		
		$obj = new \StdClass();
		$obj->foo = 'bar';
		$this->cache->set('anObject', $obj);
		$this->assertEquals(
			$obj, $this->cache->get('anObject'),
			'Le cache stocke et retrouve des objets personnalisés'
		);
		
		//Valeur de retour pour clé non existante.
		$this->assertFalse($this->cache->get('aNonExistantKey'));
		$this->assertEquals('foobar', $this->cache->get('aNonExistantKey', 'foobar'));
		
		//Manipulation de l'index pour simuler une ancienne valeur.
		$indexProp = new \ReflectionProperty(get_class($this->cache), 'index');
		$indexProp->setAccessible(true);
		$index = $indexProp->getValue($this->cache);
		$index['aKey']['time'] = time() - 11;
		$indexProp->setValue($this->cache, $index);
		$this->assertFalse($this->cache->get('aKey'), 'Les clés de cache expirent correctement');
	}
	
	public function testHas()
	{
		$this->cache->set('aKey', 'aValue');
		$this->assertTrue($this->cache->has('aKey'));
		$this->assertFalse($this->cache->has('aNonExistantKey'));
	}
	
	public function testAdd()
	{
		$this->cache->set('aKey', 'aValue');
		$this->assertFalse($this->cache->add('aKey', 'anOtherValue'));
		$this->assertTrue($this->cache->add('aNonExistantKey', 'aValue'));
	}
	
	public function testReplace()
	{
		$this->cache->set('aKey', 'aValue');
		$this->assertTrue($this->cache->replace('aKey', 'anOtherValue'));
		$this->assertFalse($this->cache->replace('aNonExistantKey', 'aValue'));
	}
	
	public function testIncrement()
	{
		$this->cache->set('aKey', 2);
		$this->assertEquals(3, $this->cache->increment('aKey'));
		$this->assertEquals($this->cache->get('aKey'), 3);
		
		$this->assertEquals(6, $this->cache->increment('aKey', 3));
		$this->assertEquals($this->cache->get('aKey'), 6);
	}
	
	public function testDecrement()
	{
		$this->cache->set('aKey', 6);
		$this->assertEquals(5, $this->cache->decrement('aKey'));
		$this->assertEquals($this->cache->get('aKey'), 5);
		
		$this->assertEquals(2, $this->cache->decrement('aKey', 3));
		$this->assertEquals($this->cache->get('aKey'), 2);
	}
	
	public function testDelete()
	{
		$this->cache->set('aKey', 'aValue');
		$this->cache->delete('aKey');
		$this->assertFalse($this->cache->has('aKey'));
		
		$this->cache->set('aKey', 'aValue');
		$this->cache->set('anOtherKey', 'aValue');
		$this->cache->set('theKey', 'aValue');
		$this->cache->delete('a*Key');
		$this->assertFalse($this->cache->has('aKey'));
		$this->assertFalse($this->cache->has('anOtherKey'));
		$this->assertTrue($this->cache->has('theKey'));
	}
	
	public function testFlush()
	{
		$this->cache->set('aKey', 'aValue');
		$this->cache->set('anOtherKey', 'aValue');
		$this->cache->flush();
		$this->assertFalse($this->cache->has('aKey'));
		$this->assertFalse($this->cache->has('anOtherKey'));
	}
	
	public function testGetIndex()
	{
		$this->cache->set('aKey', 'aValue');
		$this->cache->set('anOtherKey', 'aValue', 100);
		$index = $this->cache->getIndex();
		
		$this->assertTrue(is_array($index));
		$this->assertEquals($index['aKey']['lifetime'], 600);
		$this->assertEquals($index['anOtherKey']['lifetime'], 100);
	}
	
	protected function setUp()
	{
		$this->cache = $this->getCache();
	}
	
	protected function tearDown()
	{
		$this->cache = null;
	}
	
	abstract protected function getCache();
}
