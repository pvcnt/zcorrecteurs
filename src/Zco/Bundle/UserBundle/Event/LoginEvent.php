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

namespace Zco\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class LoginEvent extends Event
{
	protected $user;
	protected $remember;
	private $state;
	
	public function __construct(\Utilisateur $user, $remember, $state)
	{
		$this->user     = $user;
		$this->state    = $state;
		$this->remember = $remember;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function getState()
	{
		return $this->state;
	}
	
	public function isRemember()
	{
		return $this->remember;
	}
}
