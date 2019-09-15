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
 * Valide une chaine de caractères. Inclut des validateurs suivant la longueur
 * de la chaine de caractères.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Validator_String extends Validator
{
	protected function configure($options, $messages)
	{
		$this->addMessage('min_length', '« %value% » est trop courte (%min_length% caractères minimum).');
		$this->addMessage('max_length', '« %value% » est trop longue (%max_length% caractères maximum).');

		$this->addOption('min_length');
		$this->addOption('max_length');
	}

	protected function doClean($value)
	{
		$value = (string)$value;
		if($this->hasOption('min_length') && mb_strlen($value) < $this->getOption('min_length'))
		{
			$this->addError('min_length');
		}
		if($this->hasOption('max_length') && mb_strlen($value) > $this->getOption('max_length'))
		{
			$this->addError('max_length');
		}
		return $value;
	}
}