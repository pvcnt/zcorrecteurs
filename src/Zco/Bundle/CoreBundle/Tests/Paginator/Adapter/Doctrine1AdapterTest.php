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

namespace Zco\Bundle\CoreBundle\Tests\Paginator\Adapter
{
use Zco\Bundle\CoreBundle\Paginator\Adapter\Doctrine1Adapter;

class Doctrine1AdapterTest extends \PHPUnit_Framework_TestCase
{
	protected $conn;
	protected $table;
	protected $adapter;
	
	public function testSupports()
	{
		$adapter = $this->getAdapter();
		$this->assertTrue(
			$adapter->supports(new \Doctrine_Query()),
			'L\'adaptateur pour les requêtes Doctrine supporte les requêtes Doctrine'
		);
		$this->assertFalse(
			$adapter->supports(new \StdClass()),
			'L\adaptateur pour les requêtes Doctrine ne supporte pas les autres objets'
		);
	}
	
	public function testCount()
	{
		$adapter = $this->getAdapter();
		$this->assertEquals(
			10, $adapter->count(\Doctrine_Core::getTable('TestEntity')->getQuery())
		);
	}
	
	public function testSlice()
	{
		$adapter = $this->getAdapter();
		
		$collection = $adapter->slice($this->table->getQuery(), 5, null);
		$this->assertEquals(
			5, count($collection), 
			'Le découpage sans longueur spécifiée prend toutes les lignes jusqu\'à la fin'
		);
		$this->assertEquals(
			5, $collection->offsetGet(0)->colonne, 
			'Le découpage sans longueur spécifiée commence au bon indice'
		);
		
		$collection = $adapter->slice($this->table->getQuery(), 5, 2);
		$this->assertEquals(
			2, count($collection), 
			'Le découpage avec longueur spécifiée s\'arrête à l\'endroit indiqué'
		);
		$this->assertEquals(
			5, $collection->offsetGet(0)->colonne, 
			'Le découpage avec longueur spécifiée commence au bon indice'
		);
	}
	
	protected function setUp()
	{
		if (!class_exists('\Doctrine_Core'))
		{
			$this->markTestSkipped('Ce test requiert la présence de Doctrine1');
		}
		if (!class_exists('\PDO') || !in_array('sqlite', \PDO::getAvailableDrivers()))
		{
			$this->markTestSkipped('Ce test requiert le pilote SQLite pour PDO');
		}
		
		$this->adapter = $this->getAdapter();
		$this->conn = \Doctrine_Manager::connection(new \PDO('sqlite::memory:'), 'doctrine');
		
		//Crée une table de tests et y insère des données.
		$this->conn->getDbh()->exec('DROP TABLE IF EXISTS tests');
		$this->table = \Doctrine_Core::getTable('TestEntity');
		$this->conn->export->createTable($this->table->getTableName(), $this->table->getColumns());
		
		for ($i = 0; $i < 10; $i++)
		{
			$t = new \TestEntity();
			$t->colonne = $i;
			$t->save();
		}
	}

	protected function tearDown()
	{
		$this->conn = null;
		$this->table = null;
		$this->adapter = null;
	}
	
	protected function getAdapter()
	{
		return new Doctrine1Adapter();
	}
}
} /* namespace Zco\Bundle\CoreBundle\Tests\Paginator\Adapter */

namespace
{
class TestEntityTable extends \Doctrine_Table
{
	public function getQuery()
	{
		return $this->createQuery()->select('colonne');
	}
}

class TestEntity extends \Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('tests');
		$this->hasColumn('colonne', 'integer', null, array(
			 'type' => 'integer',
			 'notnull' => true,
		));
		$this->option('collate', 'utf8_unicode_ci');
		$this->option('charset', 'utf8');
	}
}
}