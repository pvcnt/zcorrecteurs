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
 * Vérifie que la valeur corresponde à un nombre flottant et la convertit en
 * flottant systématiquement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Validator_Number extends Validator
{
	protected function configure($options, $messages)
	{
		$this->addMessage('min_value', '« %value% » doit être supérieure à %min_value%.');
		$this->addMessage('max_value', '« %value% » doit être inférieure à %max_value%.');
		$this->setMessage('invalid', '« %value% » doit être un nombre entier.');

		$this->addOption('min_value');
		$this->addOption('max_value');
	}

	public function doClean($value)
	{
		//Allow french decimal and thousand separator.
		$value = str_replace(array(',', ' '), array('.', ''), $value);

		if($this->getOption('required') && !is_numeric($value))
		{
			$this->addError('invalid');
		}
		$value = floatval($value);
		if($this->hasOption('min_value') && $value < $this->getOption('min_value'))
		{
			$this->addError('min_value');
		}
		if($this->hasOption('max_value') && $value > $this->getOption('max_value'))
		{
			$this->addError('max_value');
		}
		return $value;
	}
}