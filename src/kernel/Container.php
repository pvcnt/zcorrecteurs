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

/**
 * Simple class to provide a singleton on the dependency injection layer.
 * Provides quick access over services and parameters over all the application.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Container
{
	protected static $instance;

	/**
	 * Get the instance of the container class.
	 *
	 * @return ContainerInterface
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new ContainerBuilder;
		}

		return self::$instance;
	}

	/**
	 * Defines the instance of the container. This is not a very
	 * proper way to do this, but this allow customization of the class on the
	 * fly and allow using the PHP cache of the container.
	 *
	 * @param ContainerInterface $container
	 */
	public static function setInstance(ContainerInterface $container)
	{
		self::$instance = $container;
	}

	/**
	 * Shortcut to get a service without using the container instance.
	 *
	 * @param string $service		The service name.
	 * @return ContainerInterface			The required service.
	 */
	public static function getService($service)
	{
		return self::getInstance()->get($service);
	}

	/**
	 * Shortcut to get the value of a parameter without using the
	 * container instance.
	 *
	 * @param string $parameter		The parameter name.
	 * @return mixed				The parameter value.
	 */
	public static function getParameter($parameter)
	{
		return self::getInstance()->getParameter($parameter);
	}
}