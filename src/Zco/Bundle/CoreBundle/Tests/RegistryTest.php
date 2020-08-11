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

use Zco\Bundle\CoreBundle\Registry;
use Zco\Bundle\CoreBundle\Cache\MemoryCache;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	protected $registry;
	
	public function testGetSet()
	{
		$this->registry->set('aKey', 'aValue');
		$this->assertEquals(
			'aValue', $this->registry->get('aKey'),
			'Le registre stocke et retrouve les types primitifs'
		);
		
		$obj = new \StdClass();
		$obj->foo = 'bar';
		$this->registry->set('anObject', $obj);
		$this->assertEquals(
			$obj, $this->registry->get('anObject'),
			'Le registre stocke et retrouve des objets personnalisés'
		);
		
		$this->assertFalse($this->registry->get('aNonExistantKey'));
		$this->assertEquals('foobar', $this->registry->get('aNonExistantKey', 'foobar'));
	}
	
	public function testDelete()
	{
		$this->registry->set('aKey', 'aValue');
		$this->registry->delete('aKey');
		$this->assertFalse($this->registry->get('aKey'));
	}
	
	protected function setUp()
	{
		if (!class_exists('\PDO') || !in_array('sqlite', \PDO::getAvailableDrivers()))
		{
			$this->markTestSkipped('Ce test requiert le pilote SQLite pour PDO');
		}
		
		$dbh = \Doctrine_Manager::connection(new \PDO('sqlite::memory:'))->getDbh();
		$dbh->exec('DROP TABLE IF EXISTS registry');
		$dbh->exec('CREATE TABLE registry(registry_key VARCHAR(50) PRIMARY KEY, registry_value TEXT)');
		
		$this->registry = $this->getRegistry();
	}

	protected function tearDown()
	{
		$this->registry = null;
		\Doctrine_Manager::resetInstance();
	}
	
	protected function getRegistry()
	{
		return new Registry(new MemoryCache(600), '');
	}
}
