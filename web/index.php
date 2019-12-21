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

define('BASEPATH', realpath(__DIR__.'/..'));
define('APP_PATH', BASEPATH.'/app');

require_once __DIR__.'/../app/autoload.php';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

if (in_array(BASEPATH, array('/home/web/zcorrecteurs.fr/prod', '/home/web/zcorrecteurs.fr/test')))
{
	$kernel = new AppKernel('prod', false);
	$kernel->loadClassCache();
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
