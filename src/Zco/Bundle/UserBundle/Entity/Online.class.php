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

	public function getUserId()
	{
		return $this->user_id;
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
	
	public function getAction()
	{
		return $this->action;
	}
	
	public function getActionIdentifier()
	{
		return $this->action_identifier;
	}
	
	public function isAuthenticated()
	{
		return $this->user_id > 0;
	}
}