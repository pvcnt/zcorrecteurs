<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
 * Classe permettant de gérer facilement la configuration du site via des
 * fichiers YAML (gère leur compilation et la récupération des informations
 * qu'ils procurent). S'occupe de merger les configurations entre celles
 * génériques de l'application et les configurations par module.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Config
{
	private static $config = array();
	private static $loaded = array();

	/**
	 * Charge une configuration en mémoire.
	 * @param string $file		Le nom du fichier.
	 * @return boolean			A-t-on recompilé ?
	 */
	public static function load($file, $force = false)
	{
		if(!in_array($file, self::$loaded) || $force == true)
		{
			$handlerName = 'Config_Handler_'.ucfirst($file);
			if(!class_exists($handlerName))
				throw new RuntimeException(sprintf('No handler %s for file %s.yml.', $handlerName, $file));
			$handler = new $handlerName($file);
			$d = Container::getParameter('kernel.cache_dir').'/config/';

			if ($handler->isRecursive())
			{
        		$bundle = Container::getService('request')->attributes->get('_bundle');
				$bundle = Container::getService('kernel')->getBundle($bundle);
        		
				$file_cache = $d.$bundle->getName().'.'.str_replace('/', '_', $file).'.compiled.php';
				$file_module = $bundle->getPath().'/Resources/config/'.$file.'.yml';
			}
			else
			{
				$file_cache = $d.str_replace('/', '_', $file).'.compiled.php';
			}
			$file_root = APP_PATH.'/config/'.$file.'.yml';

			//On ne recompile que si nécessaire.
			if(
				!is_file($file_cache) ||
				($handler->isRecursive() == true && is_file($file_module) && filemtime($file_module) > filemtime($file_cache)) ||
				(is_file($file_root) && filemtime($file_root) > filemtime($file_cache))
			)
			{
				if (!is_dir($d))
				{
					mkdir($d, 0777, true);
				}
				$handler->compile($file_cache);
			}

			//On inclut le fichier de cache, et on marque la
			//configuration comme chargée.
			include($file_cache);
			self::$loaded[] = $file;
		}

		return false;
	}

	/**
	 * Récupère la valeur d'une configuration.
	 * @param string $index		Le clé de configuration à charger.
	 * @return array
	 */
	public static function get($index)
	{
		if(!isset(self::$config[$index]))
		{
			try {
				self::load($index);
			}
			catch(Exception $e) {
				throw new InvalidArgumentException(sprintf('Config %s.yml must to be loaded before it can be used.', $index));
			}
		}

		return self::$config[$index];
	}

	/**
	 * Enregistre une configuration.
	 * @param string $index		La clé de configuration.
	 * @param mixed $value		La valeur à donner.
	 */
	public static function set($index, $value)
	{
		self::$config[$index] = $value;
	}
}
