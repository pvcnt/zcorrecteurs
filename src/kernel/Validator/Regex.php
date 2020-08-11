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
