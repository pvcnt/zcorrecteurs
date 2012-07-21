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

namespace Zco\Bundle\SentryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder()
	{
		$builder = new TreeBuilder();

		$builder->root('zco_sentry')
			->children()
				->scalarNode('dsn')->defaultNull()->end()
				->scalarNode('level')->defaultValue('error')->end()
				->booleanNode('auto_log_stacks')->defaultTrue()->end()
				->scalarNode('server_name')->defaultNull()->end()
				->scalarNode('resque')->defaultNull()->end()
				->arrayNode('tags')
					->prototype('scalar')->end()
				->end()
			->end()
		;

		return $builder;
	}
}
