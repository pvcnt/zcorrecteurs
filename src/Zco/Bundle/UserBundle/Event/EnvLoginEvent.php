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

namespace Zco\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Zco\Bundle\UserBundle\User\User;
use Symfony\Component\HttpFoundation\Request;

class EnvLoginEvent extends Event
{
	private $request;
	private $userId;
	private $user;
	private $state;
	private $aborted = false;
	
	public function __construct(Request $request, $userId = null, $state = User::AUTHENTICATED_ANONYMOUSLY)
	{
		$this->request = $request;
		$this->userId  = $userId;
		$this->state   = $state;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}
	
	public function getState()
	{
		return $this->state;
	}
	
	public function setUser(\Utilisateur $user, $state = null)
	{
		$this->user  = $user;
		$this->state = ($state !== null) ? $state : $this->state;
		$this->stopPropagation();
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function abort()
	{
		$this->aborted = true;
		$this->stopPropagation();
	}
	
	public function isAborted()
	{
		return $this->aborted;
	}
}
