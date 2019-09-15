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
class Validator_Choices extends Validator
{
	protected function configure($options, $messages)
	{
		$this->addOption('choices');
		$this->addOption('multiple', false);
		$this->addRequiredOption('choices');
	}

	protected function doClean($value)
	{
		if($this->getOption('multiple') && is_array($value))
		{
			foreach($value as $key => $val)
			{
				if(!in_array($val, $this->getOption('choices')))
					$this->addError('invalid');
			}
		}
		else
		{
			if(!in_array($value, $this->getOption('choices')))
				$this->addError('invalid');
		}
		return $value;
	}
}
