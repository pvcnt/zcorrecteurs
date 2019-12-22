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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * ZcoCoreExtension.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcoCoreExtension extends Extension
{
	/**
	 * Responds to the app.config configuration parameter.
	 *
	 * @param array			$configs
	 * @param ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$config = $this->processConfiguration(new Configuration(), $configs);
		$container->setParameter('zco_core.mandrill.api_key', $config['mandrill']['api_key']);
		$container->setParameter('zco_core.mandrill.track_opens', $config['mandrill']['track_opens']);
		$container->setParameter('zco_core.mandrill.track_clicks', $config['mandrill']['track_clicks']);
		$container->setParameter('zco_core.mandrill.tags', $config['mandrill']['tags']);
		
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');
		$loader->load('listeners.yml');
		$loader->load('cache.yml');
		$loader->load('templating_helpers.yml');
		
		$container->setAlias('zco_core.cache', 'zco_core.cache.'.$config['cache']['default']);
	}
}
