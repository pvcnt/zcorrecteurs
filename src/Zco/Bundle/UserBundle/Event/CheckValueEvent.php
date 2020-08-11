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

namespace Zco\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class CheckValueEvent extends Event
{
	private $value;
	private $user;
	private $acceptable;
	private $errorMessage;
	
	public function __construct($value, \Utilisateur $user = null)
	{
		$this->value = $value;
		$this->user  = $user;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function validate()
	{
		$this->acceptable = true;
	}
	
	public function reject($message = '')
	{
		$this->acceptable   = false;
		$this->errorMessage = $message;
	}
	
	public function isAcceptable()
	{
		return $this->acceptable;
	}
	
	/**
	 * Retourne le message d'erreur associé à un fichier non acceptable.
	 *
	 * @return string Le message d'erreur, éventuellement vide
	 * @throws \LogicException Si la valeur est acceptable
	 */
	public function getErrorMessage()
	{
		if ($this->isAcceptable())
		{
			throw new \LogicException('Cannot get the error message when the value is acceptable.');
		}
		
		return $this->errorMessage;
	}
}
