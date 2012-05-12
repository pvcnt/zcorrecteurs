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

namespace Zco\Bundle\CoreBundle\Tests\Paginator\View;

use Zco\Bundle\CoreBundle\Paginator\View\PaginatorView;
use Zco\Bundle\CoreBundle\Paginator\Paginator;

class PaginatorViewTest extends \PHPUnit_Framework_TestCase
{
	public function testGetSetUri()
	{
		$view = $this->getPaginatorView();
		$view->setUri('/module/%s');
		$this->assertEquals('/module/%s', $view->getUri());
	}
	
	public function testConstructor()
	{
		$objects = array('foo', 'bar');
		$paginator = new Paginator($objects, 15);
		$view = new PaginatorView($objects, 4 /* oui, c'est un test ! */, 2, $paginator, '/module/%s');
		
		$this->assertEquals($objects, $view->getObjects());
		$this->assertEquals(4, $view->count());
		$this->assertEquals(2, $view->getNumber());
		$this->assertEquals('/module/%s', $view->getUri());
	}
	
	public function testCount()
	{
		$view = $this->getPaginatorView();
		$this->assertEquals(15, $view->count());
	}
	
	public function testCountAll()
	{
		$view = $this->getPaginatorView();
		$this->assertEquals(15, $view->countAll());
	}
	
	public function testIterator()
	{
		$view = $this->getPaginatorView();
		$valid = true;
		$c = 0;
		foreach ($view as $i => $object)
		{
			$valid = $valid && $i === $c++ && $object === 'Object '.$i;
		}
		
		$this->assertTrue($valid);
	}
	
	protected function getPaginatorView(array $objects = null, $number = 1, $uri = null)
	{
		if ($objects === null)
		{
			$objects = array();
			for ($i = 0; $i < 15; $i++)
			{
				$objects[] = 'Object '.$i;
			}
		}
		$count     = count($objects);
		$paginator = new Paginator($objects, $count);
		
		return new PaginatorView($objects, $count, $number, $paginator, $uri);
	}
}
