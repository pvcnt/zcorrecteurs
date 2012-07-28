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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ZcoSentryExtension extends Extension
{
	/**
	 * {@inheritDoc}
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		//On ne charge la configuration que si Sentry est configuré correctement.
		if (!empty($config['dsn']))
		{
			$container->setParameter('zco_sentry.client.dsn', $config['dsn']);
			$container->setParameter('zco_sentry.client.tags', $config['tags']);
			$container->setParameter('zco_sentry.client.auto_log_stacks', $config['auto_log_stacks']);
			$container->setParameter('zco_sentry.client.server_name', $config['server_name']);
			$container->setParameter('zco_sentry.handler.level', $config['level']);
			$container->setParameter('zco_sentry.client.resque', $config['resque']);

			$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
			$loader->load('services.yml');
		}
	}
}
