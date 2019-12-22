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
 * Valide une expression en fonction d'un ensemble de validateurs. Si tous les
 * validateurs renvoient une erreur, une exception est levée avec le message
 * « invalid ». Sinon la valeur passe.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Validator_Or extends Validator
{
	protected $validators = array();

	public function __construct($validators, $options, $messages)
	{
		$this->validators = $validators;
		parent::__construct($options, $messages);
	}

	protected function configure($options, $messages)
	{
	}

	protected function clean($value)
	{
		$errors = array();
		foreach($this->getValidators() as $validator)
		{
			try
			{
				$value = $validator->clean($value);
			}
			catch(Validator_Error $e)
			{
				$errors[] = $e;
			}
		}
		if(count($errors) >= count($this->getValidators()))
			$this->addError('invalid');

		return $value;
	}

	public function addValidators()
	{
		$args = func_get_args();
		$this->validators = array_merge($this->validators, $validators);
	}

	public function getValidators()
	{
		return $this->validators;
	}
}