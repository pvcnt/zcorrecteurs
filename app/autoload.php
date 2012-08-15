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

if (!class_exists('Symfony\Component\ClassLoader\UniversalClassLoader', false))
{
	include(__DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');
}

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
	'Symfony'           => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
	'Sensio'            => __DIR__.'/../vendor/bundles',
	'Doctrine\\Bundle'  => __DIR__.'/../vendor/bundles',
    'Doctrine\\Common'  => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine'          => __DIR__.'/../vendor/doctrine/lib',
	'Monolog'	        => __DIR__.'/../vendor/monolog/src',
	'Assetic'           => __DIR__.'/../vendor/assetic/src',
	'Metadata'          => __DIR__.'/../vendor/metadata/src',
	'JMS'               => __DIR__.'/../vendor/bundles',
	'Zco'	    		=> __DIR__.'/../src',
	'Knp'		        => __DIR__.'/../vendor/KnpMenu/src',
	'Knp\Bundle'        => __DIR__.'/../vendor/bundles',
	'Knp\Component'     => __DIR__.'/../vendor/knp-components/src',
	'Gaufrette'         => __DIR__.'/../vendor/gaufrette/src',
	'Imagine'           => __DIR__.'/../vendor/imagine/lib',
    'Avalanche'         => __DIR__.'/../vendor/bundles',
    'FOS'               => __DIR__.'/../vendor/bundles',
	'Mopa'              => __DIR__.'/../vendor/bundles',
	'Bazinga'			=> __DIR__.'/../vendor/bundles',
    'Geocoder'			=> __DIR__.'/../vendor/geocoder/src',
));

$loader->registerPrefixes(array(
	'Twig_Extensions_' 	 => __DIR__.'/../vendor/twig-extensions/lib',
	'Twig_'			     => __DIR__.'/../vendor/twig/lib',
	'Doctrine_'          => __DIR__.'/../vendor/doctrine1',
	'Resque'             => __DIR__.'/../vendor/resque/lib',
	'sfYaml'             => __DIR__.'/../vendor/doctrine1/vendor/sfYaml',
	'CssMin'             => __DIR__.'/../vendor/cssmin',
	'JavascriptMinifier' => __DIR__.'/../vendor/jsmin',
));

$prefixFallbacks = array(__DIR__.'/../src/kernel');

// intl
if (!function_exists('intl_get_error_code'))
{
	require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
	$prefixFallbacks[] = __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs';
}

$loader->registerPrefixFallbacks($prefixFallbacks);

$loader->registerNamespaceFallbacks(array(
	__DIR__.'/../src',
));
$loader->register();

// Include useful files
include(__DIR__.'/../src/kernel/Zingle.php');
include(__DIR__.'/lib/functions.php');
include(__DIR__.'/config/constants.php');
include(__DIR__.'/lib/zCorrecteurs.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive)
require_once __DIR__.'/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/lib/swift_init.php');
