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
 * Représente un visiteur connecté sur le site.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Online extends BaseOnline
{
	private $botName;
	
	public function isBot()
	{
		$this->botName = $this->getTable()->getBotName($this->user_agent);
		
		return $this->botName !== null;
	}
	
	public function getBotName()
	{
		return $this->botName;
	}
	
	public function getUser()
	{
		return $this->User;
	}
	
	public function getCategory()
	{
		return $this->Category;
	}
	
	public function getLastActionDate()
	{
		return $this->last_action;
	}
	
	public function getIpAddress()
	{
		return $this->ip;
	}
	
	public function isAuthenticated()
	{
		return $this->user_id > 0;
	}
}