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

namespace Zco\Bundle\TwitterBundle\Service;

/**
 * Utilisation de l'API de bit.ly (liens courts).
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
*/
class BitLy
{
	public function __construct($login, $key, $endPoint)
	{
		$this->login    = $login;
		$this->key      = $key;
		$this->endPoint = $endPoint;
	}

	public function shorten($url)
	{
		$params = array(
			'login'   => $this->login,
			'apiKey'  => $this->key,
			'format'  => 'txt',
			'longUrl' => $url,
			'domain'  => 'j.mp',
		);
		$qs = '?';
		foreach ($params as $k => &$v)
		{
			$qs .= rawurlencode($k).'='.rawurlencode($v).'&';
		}
		$qs = substr($qs, 0, -1);
		$rep = @file_get_contents($this->endPoint.'/shorten'.$qs);

		return $rep ? trim($rep) : $url;
	}
}

