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
 * Adresse courriel n'ayant pas le droit d'être utilisée par les utilisateurs 
 * comme adresse associée à leur compte (en réalité on stocke uniquement des 
 * TLD).
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BannedEmail extends BaseBannedEmail
{
	public function getId()
	{
		return $this->id;
	}
	
	public function getUserId()
	{
		return $this->utilisateur_id;
	}
	
	public function setUserId($userId)
	{
		$this->utilisateur_id = $userId;
	}
	
	public function getUser()
	{
		return $this->User;
	}
	
	public function setUser(\Utilisateur $user)
	{
		$this->User = $user;
	}
	
	public function getEmail()
	{
		return $this->mail;
	}
	
	public function setEmail($email)
	{
		$this->mail = $email;
	}
	
	public function getReason()
	{
		return $this->raison;
	}
	
	public function setReason($reason)
	{
		$this->raison = $reason;
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addGetterConstraint('email', new Constraints\NotBlank());
	}
}