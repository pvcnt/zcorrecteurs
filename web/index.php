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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

define('BASEPATH', realpath(__DIR__ . '/..'));
define('APP_PATH', BASEPATH . '/app');

$env = getenv('ENVIRONMENT') ?: 'prod';
$debug = 'true' === getenv('DEBUG') && 'prod' !== $env;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../app/autoload.php';
include_once __DIR__ . '/../app/bootstrap.php.cache';

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);