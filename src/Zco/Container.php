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

namespace Zco;

use Doctrine\Common\Cache\CacheProvider;
use Imagine\Image\ImagineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Simple class to provide a singleton on the dependency injection layer.
 * Provides quick access over services and parameters over all the application.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class Container
{
    private static $instance;

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
     * @param string $service The service identifier.
     * @param int $invalidBehavior The behavior when the service does not exist.
     * @return object The required service.
     */
    public static function get($service, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return self::instance()->get($service, $invalidBehavior);
    }

    public static function imagine(): ImagineInterface
    {
        return self::instance()->get('imagine');
    }

    public static function request(): Request
    {
        return self::instance()->get('request_stack')->getCurrentRequest();
    }

    public static function cache(): CacheProvider
    {
        return self::instance()->get('cache');
    }

    /**
     * Get the instance of the container class.
     *
     * @return ContainerInterface
     */
    private static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ContainerBuilder;
        }

        return self::$instance;
    }
}