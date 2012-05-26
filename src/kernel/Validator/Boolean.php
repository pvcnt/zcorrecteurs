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
 * Vérifie que la valeur corresponde à une valeur booléenne.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Validator_Boolean extends Validator
{
	protected function configure($options, $messages)
	{
		$this->addOption('true_values', array('true', 't', 'yes', 'y', 'on', '1', true, 1));
		$this->addOption('false_values', array('false', 'f', 'no', 'n', 'off', '0', false, 0));
		$this->setOption('required', false);
	}

	protected function doClean($value)
	{
		if(in_array($value, $this->getOption('true_values')))
		{
			return true;
		}
		if(in_array($value, $this->getOption('false_values')))
		{
			return false;
		}
		else
		{
			$this->addError('invalid');
		}
	}
}