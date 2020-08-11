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
class Validator_Date extends Validator
{
	protected function configure($options, $messages)
	{
		$this->addMessage('min_date', 'La date doit être après %min_date%.');
		$this->addMessage('max_date', 'La date doit être avant %min_date%.');

		$this->addOption('with_time', false);
		$this->addOption('max_date');
		$this->addOption('min_date');
		$this->addOption('format');
		$this->addOption('output_format', 'Y-m-d');
	}

	protected function doClean($value)
	{
		if(is_array($value))
		{
			$value = array_merge(array('hour' => 0, 'minute' => 0, 'second' => 0, 'month' => 1, 'day' => 1, 'year' => date('Y')), $value);
			$date = mktime($value['hour'], $value['minute'], $value['second'], $value['month'], $value['day'], $value['year']);
		}
		elseif(is_numeric($value))
			$time = $value;
		elseif(is_string($value) && $this->getOption('format'))
		{

		}
		elseif(is_string($value) && ($tmp = strtotime($value)) !== false)
			$time = $tmp;
		else
			throw new InvalidArgumentException('Value received by '.get_class($this).' is invalid.');

		if($this->getOption('min_date') && $time < $this->getOption('min_date'))
			$this->addError('min_date');
		if($this->getOption('max_date') && $time > $this->getOption('max_date'))
			$this->addError('max_date');

		if($this->getOption('output_format'))
			return date($this->getOption('format'), $time);
		else
			return $time;
	}
}
