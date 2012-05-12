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
 * Vérifie que la valeur corresponde à une regex donnée (au format PCRE).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>author vincent1870
 */
class Validator_Regex extends Validator_String
{
	protected function configure($options, $messages)
	{
		parent::configure($options, $messages);
		$this->addRequiredOption('pattern');
	}

	protected function doClean($value)
	{
		$value = parent::doClean($value);
		$pattern = $this->getOption('pattern');
		//TODO : ajout de délimiteurs de façon automatique.
		/*if($pattern[0] != $pattern[mb_strlen($pattern)-1])
		{
			$pattern = '`'.$pattern.'`';
		}*/
		if(!preg_match($pattern, $value))
		{
			$this->addError('invalid');
		}
		return $value;
	}
}
