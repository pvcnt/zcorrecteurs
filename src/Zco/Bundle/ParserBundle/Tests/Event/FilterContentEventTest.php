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

namespace Zco\Bundle\ParserBundle\Tests\Event;

use Zco\Bundle\ParserBundle\Event\FilterContentEvent;

class FilterContentEventTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetContent()
    {
		$event = new FilterContentEvent('foo');
		
		$this->assertEquals('foo', $event->getContent());
		
		$event->setContent('bar');
		$this->assertEquals('bar', $event->getContent());
    }

	public function testSetGetOptions()
    {
		$options = array('foo' => 'bar');
		$event   = new FilterContentEvent('', $options);
		
		$this->assertEquals('bar', $event->getOption('foo'));
		$this->assertEquals(false, $event->getOption('bar', false));
		
		$this->assertEquals($options, $event->getOptions());
    }

	public function testStopProcessing()
    {
		$event = new FilterContentEvent('');
		
		$this->assertFalse($event->isProcessingStopped());
		
		$event->stopProcessing();
		$this->assertTrue($event->isProcessingStopped());
    }
}