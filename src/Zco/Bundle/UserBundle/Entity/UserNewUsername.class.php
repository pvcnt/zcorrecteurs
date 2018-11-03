<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Component\Validator\Constraints as SfConstraints;
use Zco\Bundle\UserBundle\Validator\Constraints as ZcoConstraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * UserNewUsername
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserNewUsername extends BaseUserNewUsername
{
	private $autoValidated;
	
	public function preInsert($event)
	{
		$this->changement_date = new \Doctrine_Expression('NOW()');
		$this->status = $this->autoValidated ? CH_PSEUDO_AUTO : CH_PSEUDO_ATTENTE;
		if ($this->User && !$this->old_username)
		{
			$this->old_username = $this->User->getUsername();
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getDate()
	{
		return $this->changement_date;
	}
	
	public function getResponseDate()
	{
		return $this->changement_date_reponse;
	}
	
	public function setResponseDate($date)
	{
		$this->changement_date_reponse = $date;
	}
	
	public function setOldUsername($username)
	{
		$this->old_username = $username;
	}
	
	public function getOldUsername()
	{
		return $this->old_username;
	}
	
	public function setNewUsername($username)
	{
		$this->new_username = $username;
	}
	
	public function getNewUsername()
	{
		return $this->new_username;
	}
	
	public function getReason()
	{
		return $this->reason;
	}
	
	public function getAdminResponse()
	{
		return $this->response;
	}
	
	public function setAdminResponse($response)
	{
		$this->response = $response;
	}
	
	public function setAutoValidated($validated)
	{
		$this->autoValidated = (bool) $validated;
	}
	
	public function isAutoValidated()
	{
		return $this->autoValidated;
	}
	
	public function setUser(\Utilisateur $user)
	{
		$this->User = $user;
	}
	
	public function getUser()
	{
		return $this->User;
	}
	
	public function setUserId($userId)
	{
		$this->user_id = $userId;
	}
	
	public function getUserId()
	{
		return $this->user_id;
	}
	
	public function setAdmin(\Utilisateur $user)
	{
		$this->Admin = $user;
	}
	
	public function getAdmin()
	{
		return $this->Admin;
	}
	
	public function setAdminId($userId)
	{
		$this->admin_id = $userId;
	}
	
	public function getAdminId()
	{
		return $this->admin_id;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addGetterConstraint('newUsername', new ZcoConstraints\Username(array(
			'groups' => array('create'),
		)));
		$metadata->addGetterConstraint('reason', new SfConstraints\NotBlank(array(
			'groups' => array('create'),
		)));
		$metadata->addGetterConstraint('adminResponse', new SfConstraints\NotBlank(array(
			'groups' => array('answer'),
		)));
		$metadata->addGetterConstraint('status', new SfConstraints\Choice(array(
			'choices' => array(CH_PSEUDO_ACCEPTE, CH_PSEUDO_REFUSE),
			'min'     => 1,
			'max'     => 1,
			'groups'  => array('answer'),
		)));
	}
}