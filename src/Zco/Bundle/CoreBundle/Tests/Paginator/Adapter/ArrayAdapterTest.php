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

namespace Zco\Bundle\CoreBundle\Tests\Paginator\Adapter;

use Zco\Bundle\CoreBundle\Paginator\Adapter\ArrayAdapter;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
	protected $adapter;
	
	public function testSupports()
	{
		$this->assertTrue(
			$this->adapter->supports(array('foo', 'bar')),
			'L\'adaptateur pour les tableaux supporte les tableaux'
		);
		
		$this->assertFalse(
			$this->adapter->supports(new \StdClass()),
			'L\adaptateur pour les tableaux ne supporte pas les objets'
		);
	}
	
	public function testCount()
	{
		$this->assertEquals(2, $this->adapter->count(array('foo', 'bar')));
	}
	
	public function testSlice()
	{
		$this->assertEquals(
			array('bar', 'foobar'), 
			$this->adapter->slice(array('foo', 'bar', 'foobar'), 1, null),
			'Le découpage sans longueur spécifiée prend tout le tableau jusqu\'à la fin'
		);
		
		$this->assertEquals(
			array('bar'), 
			$this->adapter->slice(array('foo', 'bar', 'foobar'), 1, 1),
			'Le découpage avec longueur spécifiée s\'arrête à l\'endroit indiqué'
		);
	}
	
	protected function setUp()
	{
		$this->adapter = $this->getAdapter();
	}
	
	protected function tearDown()
	{
		$this->adapter = null;
	}
	
	protected function getAdapter()
	{
		return new ArrayAdapter();
	}
}
