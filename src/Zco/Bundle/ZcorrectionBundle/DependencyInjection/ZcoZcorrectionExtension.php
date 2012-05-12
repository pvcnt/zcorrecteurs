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

namespace Zco\Bundle\ZcorrectionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * ZcoZcorrectionExtension.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcoZcorrectionExtension extends Extension
{
	/**
	 * Responds to the app.config configuration parameter.
	 *
	 * @param array			$configs
	 * @param ContainerBuilder $container
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');
		
		$processor	   = new Processor();
		$configuration = new Configuration();
		$config		   = $processor->processConfiguration($configuration, $configs);
		
		$container->setParameter('zco_zcorrection.mcrypt_key', $config['mcrypt_key']);
		$container->setParameter('zco_zcorrection.drupal_username', $config['drupal_username']);
		$container->setParameter('zco_zcorrection.drupal_password', $config['drupal_password']);
	}
}
