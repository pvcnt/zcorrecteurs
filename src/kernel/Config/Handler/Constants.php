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

/**
 * Parseur de configuration générant des constantes PHP à partir
 * d'un fichier de définition YAML. Les constantes peuvent être
 * modifiées via une interface du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Config_Handler_Constants extends Config_Handler
{
	protected $recursive = false;

	private function def($array, $key, $alt='')
	{
		return (in_array($key, array_keys($array))) ? $array[$key] : $alt;
	}

	protected function parse()
	{
		$data = $this->parseYaml();

		$contenu = '';
		foreach($data['constants'] as $const => $attribs)
		{
			$const = addcslashes($const, "\\'");

			$valeur = $this->def($attribs, 'value', '0');
			if(is_bool($valeur))
				$valeur = ($valeur) ? 1 : 0;
			else if(!is_numeric($valeur))
				$valeur = '"' . addcslashes($valeur, '\\"') . '"';

			$contenu .= "define('$const', $valeur);\n";
		}

		return $this->header().$contenu;
	}
}