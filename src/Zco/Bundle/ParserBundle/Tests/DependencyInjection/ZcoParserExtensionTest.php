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

namespace Zco\Bundle\ParserBundle\Tests\DependencyInjection;

use Zco\Bundle\ParserBundle\DependencyInjection\ZcoParserExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ZcoParserExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithDefault()
    {
		$container = new ContainerBuilder();
		$loader = new ZcoParserExtension();

		$loader->load(array(), $container);
		$this->assertTrue($container->hasDefinition('zco_parser.parser'));
		$this->assertTrue($container->hasDefinition('zco_parser.parser.zcode'));
		$this->assertTrue($container->hasDefinition('zco_parser.parser.sdz'));
		$this->assertTrue($container->hasDefinition('zco_parser.feature.core'));
		$this->assertTrue($container->hasDefinition('zco_parser.feature.cache'));
		$this->assertTrue($container->hasDefinition('zco_parser.feature.smilies'));
    }
}
