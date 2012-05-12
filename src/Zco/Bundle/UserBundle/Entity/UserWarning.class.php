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

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Avertissement laissé à un utilisateur (parfois appelé pourcentage).
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserWarning extends BaseUserWarning
{
	public function preInsert($event)
	{
		$this->date = new \Doctrine_Expression('NOW()');
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function setAdminId($adminId)
	{
		$this->admin_id = $adminId;
	}
	
	public function getAdminId()
	{
		return $this->admin_id;
	}
	
	public function setAdmin(\Utilisateur $user)
	{
		$this->Admin = $user;
	}
	
	public function getAdmin()
	{
		return $this->Admin;
	}
	
	public function setUserId($userId)
	{
		$this->user_id = $userId;
	}
	
	public function getUserId()
	{
		return $this->user_id;
	}
	
	public function setUser(\Utilisateur $user)
	{
		$this->User = $user;
	}
	
	public function getUser()
	{
		return $this->User;
	}
	
	public function setPercentage($percentage)
	{
		$this->percentage = (int) $percentage;
	}
	
	public function getPercentage()
	{
		return $this->percentage;
	}
	
	public function setReason($reason)
	{
		$this->reason = $reason;
	}
	
	public function getReason()
	{
		return $this->reason;
	}
	
	public function hasReason()
	{
		return !empty($this->reason);
	}
	
	public function setAdminReason($reason)
	{
		$this->admin_reason = $reason;
	}
	
	public function getAdminReason()
	{
		return $this->admin_reason;
	}
	
	public function setLink($link)
	{
		$this->link = $link;
	}
	
	public function getLink()
	{
		return $this->link;
	}
	
	public function hasLink()
	{
		return !empty($this->link);
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addGetterConstraint('adminReason', new Constraints\NotBlank());
		$metadata->addGetterConstraint('link', new Constraints\Url());
	}
}