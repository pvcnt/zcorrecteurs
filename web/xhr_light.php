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

/**
 * Frontend très léger pour les requêtes ajax ne nécessitant rien de particulier.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

define('BASEPATH', __DIR__.'/..');
define('APP_PATH', BASEPATH.'/app');

$acts = array('suivi_upload');
if (!isset($_POST['act']) OR !in_array($_POST['act'], $acts))
{
	header('HTTP/1.0 404 Not Found');
	readfile(APP_PATH.'/Resources/TwigBundle/views/Exception/error404.html.twig');
	exit();
}
$act = $_POST['act'];

if ($act === 'suivi_upload')
{
	if (!isset($_POST['key']))
	{
		exit('ERREUR');
	}
	
	$ok = false;
	$data = apc_fetch(ini_get('apc.rfc1867_prefix').(int)$_POST['key'], $ok);
	$ok && $data['current_time'] = $_SERVER['REQUEST_TIME'];
	exit($ok ? json_encode($data) : 'ERREUR');
}