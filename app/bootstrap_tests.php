<?php

define('BASEPATH', realpath(__DIR__.'/..'));
define('APP_PATH', BASEPATH.'/app');

if (!is_file(__DIR__.'/config/parameters.yml'))
{
	copy(__DIR__.'/config/parameters.sample.yml', __DIR__.'/config/parameters.yml');
}
if (!is_file(__DIR__.'/config/constants.yml'))
{
	copy(__DIR__.'/config/constants.sample.yml', __DIR__.'/config/constants.yml');
}

require_once __DIR__.'/bootstrap.php.cache';
