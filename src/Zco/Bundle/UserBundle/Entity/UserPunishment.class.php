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

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Sanction laissée à un utilisateur.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserPunishment extends BaseUserPunishment
{
	public function preInsert($event)
	{
		$this->date = new \Doctrine_Expression('NOW()');
		$this->remaining_duration = $this->duration;
		$this->from_group_id = $this->User->getGroupId();
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
	
	public function setGroup(\Groupe $group)
	{
		$this->Group = $group;
	}
	
	public function getGroup()
	{
		return $this->Group;
	}
	
	public function getGroupId()
	{
		return $this->to_group_id;
	}
	
	public function getOriginalGroup()
	{
		return $this->OriginalGroup;
	}
	
	public function getOriginalGroupId()
	{
		return $this->from_group_id;
	}
	
	public function setUser(\Utilisateur $user)
	{
		$this->User = $user;
	}
	
	public function getUser()
	{
		return $this->User;
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
	
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}
	
	public function getRemainingDuration()
	{
		return $this->remaining_duration;
	}
	
	public function isUnlimited()
	{
		return empty($this->duration);
	}
	
	public function isFinished()
	{
		return $this->finished;
	}
	
	public function complete()
	{
		$this->finished = true;
		$this->remaining_duration = 0;
		$this->save();
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addGetterConstraint('reason', new Constraints\NotBlank());
		$metadata->addGetterConstraint('adminReason', new Constraints\NotBlank());
		$metadata->addGetterConstraint('link', new Constraints\Url());
		$metadata->addGetterConstraint('duration', new Constraints\Min(array('limit' => 0)));
	}
}