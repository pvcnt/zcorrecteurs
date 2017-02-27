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

/**
 * Représente un objet se chargeant de compiler une configuration
 * humaine en du code PHP.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

use Symfony\Component\Yaml\Yaml;

abstract class Config_Handler
{
	protected
		$file = null,
		$recursive = true;

	/**
	 * Constructeur de classe.
	 * @param string $file		Le fichier de configuration concerné.
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}

	/**
	 * Compile le fichier YAML en code PHP.
	 * @see self::parse()
	 * @param string $cache		Le nom du fichier de cache.
	 */
	public function compile($cache)
	{
		$ret = $this->parse();
		file_put_contents($cache, $ret);
	}

	/**
	 * Se charge de la transformation du contenu d'un fichier YAML
	 * en code PHP.
	 * @see self::compile()
	 * @return string			Le contenu parsé.
	 */
	abstract protected function parse();

	/**
	 * Parse un fichier YAML en un tableau PHP.
	 * @param string|null $file		Le nom du fichier à parser (par défaut
	 * celui lié à la classe).
	 * @return array				La configuration.
	 */
	protected function parseYaml($file = null)
	{
		if (is_null($file))
			$file = $this->file;

		if ($this->isRecursive())
		{
		    $bundle = Container::getService('request')->attributes->get('_bundle');
			$bundle = Container::getService('kernel')->getBundle($bundle);
			
			$file_module = $bundle->getPath().'/Resources/config/'.$file.'.yml';
			$file_root = APP_PATH.'/config/'.$file.'.yml';

			$config_root = is_file($file_root) ? Yaml::parse(file_get_contents($file_root)) : array();
			$config_module = is_file($file_module) ? Yaml::parse(file_get_contents($file_module)) : array();
			$config = Util::arrayDeepMerge($config_root, $config_module);
			return $config;
		}
		else
		{
			$file = APP_PATH.'/config/'.$file.'.yml';
			return is_file($file) ? Yaml::parse(file_get_contents($file)) : array();
		}
	}

	/**
	 * Retourne le header type d'un fichier parsé.
	 * @return string
	 */
	protected function header()
	{
		return '<?php'."\n".
			'/**'."\n".
		 	' * Compiled configuration file. Don\'t modify directly this file, '."\n".
		 	' * modify the '.$this->file.'.yml files instead.'."\n".
		 	' *'."\n".
			' * @package	zCorrecteurs'."\n".
			' * @subpackage	config'."\n".
			' * @last		'.date('d/m/Y H:i:s')."\n".
			' */'."\n";
	}

	/**
	 * La configuration doit-elle être parsée récursivement ?
	 * @return boolean
	 */
	public function isRecursive()
	{
		return $this->recursive;
	}
}