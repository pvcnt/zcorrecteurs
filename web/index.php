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

define('BASEPATH', realpath(__DIR__.'/..'));
define('APP_PATH', BASEPATH.'/app');

require_once __DIR__.'/../app/autoload.php';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

if (in_array(BASEPATH, array('/home/web/zcorrecteurs.fr/prod', '/home/web/zcorrecteurs.fr/test')))
{
	$kernel = new AppKernel('prod', false);
	$kernel->loadClassCache();
	
	//require_once __DIR__.'/../app/AppCache.php';
	//$kernel = new AppCache($kernel);
}
elseif (strpos(BASEPATH, '/home/web/zcorrecteurs.fr/dev') === 0)
{
	$kernel = new AppKernel('dev', false);
	$kernel->loadClassCache();
}
else
{
	$kernel = new AppKernel('dev', true);
	$kernel->loadClassCache();
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
