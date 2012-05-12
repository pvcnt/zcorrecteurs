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

namespace Zco\Bundle\CoreBundle\Tests\Paginator;

use Zco\Bundle\CoreBundle\Paginator\Paginator;

class PaginatorTest extends \PHPUnit_Framework_TestCase
{
	public function testCount()
	{
		$paginator = $this->getPaginator();
		$this->assertEquals(50, $paginator->count());
	}
	
	public function testGetNbPages()
	{
		$paginator = $this->getPaginator();
		$this->assertEquals(4, $paginator->getNbPages());
	}
	
	public function testOrphans()
	{
		$paginator = $this->getPaginator(32, 15, 2);
		$this->assertEquals(2, $paginator->getNbPages());
	}
	
	public function testCreateView()
	{
		$objects = array();
		for ($i = 15; $i < 30; $i++)
		{
			$objects[] = 'Object '.$i;
		}
		
		$paginator = $this->getPaginator();
		$view = $paginator->createView(2, '/module/%s');
		$this->assertEquals(15, $view->count());
		$this->assertEquals($objects, $view->getObjects());
		$this->assertEquals('/module/%s', $view->getUri());
	}
	
	protected function getPaginator($nbObjects = 50, $maxPerPage = 15, $orphans = 0)
	{
		$objects = array();
		for ($i = 0; $i < $nbObjects; $i++)
		{
			$objects[] = 'Object '.$i;
		}
		
		return new Paginator($objects, $maxPerPage, $orphans);
	}
}
