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

if (!class_exists('Symfony\Component\ClassLoader\UniversalClassLoader', false)) {
    include(__DIR__ . '/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');
}

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => array(__DIR__ . '/symfony/src', __DIR__ . '/bundles'),
    'Assetic'          => __DIR__ . '/assetic/src',
    'Metadata'         => __DIR__ . '/metadata/src',
    'JMS'              => __DIR__ . '/bundles',
    'Zco'              => __DIR__ . '/../src',
    'Knp'              => __DIR__ . '/KnpMenu/src',
    'Knp\Bundle'       => __DIR__ . '/bundles',
    'Knp\Component'    => __DIR__ . '/knp-components/src',
    'Avalanche'        => __DIR__ . '/bundles',
    'FOS'              => __DIR__ . '/bundles',
));

$loader->registerPrefixes(array(
    'Twig_'              => __DIR__ . '/twig/lib',
    'Doctrine_'          => __DIR__ . '/doctrine1',
    'sfYaml'             => __DIR__ . '/doctrine1/vendor/sfYaml',
    'CssMin'             => __DIR__ . '/cssmin',
    'JavascriptMinifier' => __DIR__ . '/jsmin',
));

$loader->register();