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
 * Vérifie que la valeur corresponde à une URL.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Validator_URL extends Validator_Regex
{
	protected function configure($options, $messages)
	{
		parent::configure($options, $messages);
		$this->setOption('pattern', '`^
			(https?|ftps?)://                         # http or ftp (+SSL)
			(
				([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
				|                                     #  or
				\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
			)
			(:[0-9]+)?                               # a port (optional)
			(/?|/\S+)                                # a /, nothing or a / with something
			$`ix');
	}
}