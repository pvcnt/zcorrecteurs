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

namespace Zco\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree builder.
	 *
	 * @return TreeBuilder The tree builder
	 */
	public function getConfigTreeBuilder()
	{
		$builder = new TreeBuilder();

		$builder->root('zco_core')
			->children()
				->arrayNode('mandrill')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('api_key')->defaultValue('')->end()
						->scalarNode('track_opens')->defaultValue(true)->end()
						->scalarNode('track_clicks')->defaultValue(false)->end()
						->arrayNode('tags')
							->prototype('scalar')->end()
						->end()
					->end()
				->end()
				->arrayNode('cache')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('default')->defaultValue('file')->end()
					->end()
				->end()
			->end()
		;

		return $builder;
	}
}
