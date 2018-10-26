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

use Zco\Bundle\UserBundle\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Utilisateur inscrit sur le site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Utilisateur extends BaseUtilisateur
{
	protected $rawPassword;
	
	public function __toString()
	{
		return $this->pseudo;
	}
		
	public function getId()
	{
		return $this->id;
	}
	
	public function getUsername()
	{
		return $this->pseudo;
	}
	
	public function setUsername($username)
	{
		$this->pseudo = $username;
	}
	
	public function getPassword()
	{
		return $this->mot_de_passe;
	}
	
	public function setPassword($password)
	{
		$this->mot_de_passe = $password;
	}
	
	public function getRawPassword()
	{
		return $this->rawPassword;
	}
	
	public function setRawPassword($password)
	{
		$this->rawPassword = $password;
		$this->setPassword(sha1($password));
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getNewEmail()
	{
		return $this->new_email;
	}
	
	public function setNewEmail($email)
	{
		$this->new_email = $email;
	}
		
	public function getGroupId()
	{
		return $this->groupe_id;
	}
	
	public function setGroupId($groupId)
	{
		$this->groupe_id = $groupId;
	}
	
	public function getGroup()
	{
		return $this->Groupe;
	}

	public function isTeam()
	{
		return $this->Groupe->isTeam();
	}
	
	public function isAccountValid()
	{
		return $this->valide;
	}
	
	public function setAccountValid($valid)
	{
		return $this->valide = (bool) $valid;
	}

	public function setAbsent($absent)
	{
		$this->absent = (bool) $absent;
		if (!$absent)
		{
			$this->absence_start_date = null;
			$this->absence_end_date = null;
			$this->absence_reason = '';
		}
	}
	
	public function isAbsent()
	{
		return (bool) $this->absent;
	}

	public function hasAbsenceStartDate()
	{
		return (bool) $this->absence_start_date;
	}

	public function setAbsenceStartDate($date)
	{
		$this->absence_start_date = $date;
		$this->absent = (strtotime($date) <= time());
	}

	public function getAbsenceStartDate()
	{
		return $this->absence_start_date;
	}

	public function setAbsenceEndDate($date)
	{
		$this->absence_end_date = $date;
	}
	
	public function getAbsenceEndDate()
	{
		return $this->absence_end_date;
	}
	
	public function hasAbsenceReason()
	{
		return !empty($this->absence_reason);
	}

	public function setAbsenceReason($reason)
	{
		$this->absence_reason = $reason;
	}
	
	public function getAbsenceReason()
	{
		return $this->absence_reason;
	}

	public function isAbsenceValid(ExecutionContext $context)
	{
		if (strtotime($this->absence_start_date) >= strtotime($this->absence_end_date))
		{
            $context->setPropertyPath($context->getPropertyPath() . '.absence_start_date');
            $context->addViolation('La date de début doit être avant la date de fin !', array(), null);
		}
	}
	
	public function hasCitation()
	{
		return !empty($this->citation);
	}
	
	public function getCitation()
	{
		return $this->citation;
	}
	
	public function hasSignature()
	{
		return !empty($this->signature);
	}
	
	public function getSignature()
	{
		return $this->signature;
	}
	
	public function hasBiography()
	{
		return !empty($this->biography);
	}
	
	public function getBiography()
	{
		return $this->biography;
	}
	
	public function hasTitle()
	{
		return !empty($this->title);
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function hasAvatar()
	{
		return $this->hasLocalAvatar() || $this->hasGravatar();
	}

	public function hasLocalAvatar()
	{
		return (boolean) $this->avatar;
	}

	public function hasGravatar()
	{
		return (boolean) @fopen($this->getGravatarUrl().'?d=404', 'r');
	}

	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}
	
	public function getAvatar($size = 80, $default = 'mm')
	{
		if ($this->avatar) {
			return '/uploads/avatars/'.$this->avatar;
		}

		return $this->getGravatarUrl().'?s='.$size.'&d='.$default;
	}

	protected function getGravatarUrl()
	{
		return 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email)));
	}
	
	public function getGender()
	{
		return $this->sexe;
	}
	
	public function isEmailDisplayed()
	{
		return $this->email_displayed;
	}
	
	public function isCountryDisplayed()
	{
		return $this->country_displayed;
	}
	
	public function setRegistrationHash($hash)
	{
		$this->registration_hash = $hash;
	}
	
	public function getRegistrationHash()
	{
		return $this->registration_hash;
	}
	
	public function setRegistrationDate($date)
	{
		$this->date_inscription = $date;
	}
	
	public function getRegistrationDate()
	{
		return $this->date_inscription;
	}

	public function setValidationHash($hash)
	{
		$this->validation_hash = $hash;
	}
	
	public function getValidationHash()
	{
		return $this->validation_hash;
	}
	
	public function hasBirthDate()
	{
		return !empty($this->birth_date) && $this->birth_date != '0000-00-00';
	}

	public function setBirthDate($birthdate)
	{
		$this->birth_date = $birthdate ?: null;
	}
	
	public function getBirthDate()
	{
		return $this->birth_date;
	}
	
	public function getLastActionDate()
	{
		return $this->date_derniere_visite;
	}
	
	public function getLastIpAddress()
	{
		return $this->ip;
	}
	
	public function hasPGPKey()
	{
		return !empty($this->pgp_key);
	}
	
	public function getPGPKey()
	{
		return $this->pgp_key;
	}
	
	public function getNbMessages()
	{
		return $this->forum_messages;
	}
	
	public function getNbSanctions()
	{
		return $this->nb_sanctions;
	}
	
	public function incrementPercentage($step)
	{
		$this->percentage = max(0, min($this->percentage + $step, 100));
		$this->save();
	}
	
	public function decrementPercentage($step)
	{
		$this->incrementPercentage(-$step);
	}
	
	public function getPercentage()
	{
		return $this->percentage;
	}
	
	public function hasJob()
	{
		return !empty($this->job);
	}
	
	public function getJob()
	{
		return $this->job;
	}
	
	public function hasHobbies()
	{
		return !empty($this->hobbies);
	}
	
	public function getHobbies()
	{
		return $this->hobbies;
	}
	
	public function hasWebsite()
	{
		return !empty($this->website);
	}
	
	public function getWebsite()
	{
		return $this->website;
	}

	public function hasTwitter()
	{
		return !empty($this->twitter);
	}
	
	public function getTwitter()
	{
		return $this->twitter;
	}
	
	public function hasLocalisation()
	{
		return !empty($this->localisation) && $this->localisation !== 'Inconnu';
	}
	
	public function getLocalisation()
	{
		return $this->localisation;
	}
	
	public function hasAddress()
	{
		return !empty($this->address);
	}
	
	public function getAddress()
	{
		return $this->address;
	}
	
	public function getAge()
	{
		if (!$this->birth_date)
		{
			return null;
		}
		
		return floor((time() - strtotime($this->birth_date)) / (365.25 * 3600 * 24));
	}

	public function getSecondaryGroups()
	{
		return $this->SecondaryGroups;
	}

	public function getPreferences()
	{
		return $this->Preferences;
	}
	
	public function applyPunishment(\UserPunishment $punishment)
	{
		$this->nb_sanctions += 1;
		$this->groupe_id = $punishment->getGroupId();
		$this->save();
	}
	
	public function unapplyPunishment(\UserPunishment $punishment)
	{
		$this->groupe_id = $punishment->getOriginalGroupId();
		$this->save();
	}
	
	public function applyNewUsername(\UserNewUsername $query)
	{
		$this->pseudo = $query->getNewUsername();
		$this->save();
	}
	
	public function preDelete($event)
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("
		DELETE FROM zcov2_forum_lunonlu
		WHERE lunonlu_utilisateur_id = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		DELETE FROM zcov2_utilisateurs_preferences
		WHERE preference_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		DELETE FROM zcov2_utilisateurs_ips
		WHERE ip_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		DELETE FROM zcov2_sanctions
		WHERE sanction_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		DELETE FROM zcov2_changements_pseudos
		WHERE changement_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		//Mises à jour
		$stmt = $dbh->prepare("
		UPDATE zcov2_blog_auteurs
		SET auteur_id_utilisateur = NULL
		WHERE auteur_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_blog_commentaires
		SET commentaire_id_utilisateur = NULL
		WHERE commentaire_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_blog_validation
		SET valid_id_utilisateur = NULL
		WHERE valid_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_blog_versions
		SET version_id_utilisateur = NULL
		WHERE version_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_push_corrections
		SET correction_id_correcteur = NULL
		WHERE correction_id_correcteur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_changements_pseudos
		SET changement_id_admin = NULL
		WHERE changement_id_admin = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_forum_sondages_votes
		SET vote_membre_id = NULL
		WHERE vote_membre_id = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_forum_sujets
		SET sujet_auteur = NULL
		WHERE sujet_auteur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_forum_messages
		SET message_edite_auteur = NULL
		WHERE message_edite_auteur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_forum_messages
		SET message_auteur = NULL
		WHERE message_auteur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		\Doctrine_Query::create()
			->delete('Quiz')
			->where('utilisateur_id = ?', $this->getId())
			->execute();

		\Doctrine_Query::create()
			->delete('QuizQuestion')
			->where('utilisateur_id = ?', $this->getId())
			->execute();

		\Doctrine_Query::create()
			->delete('QuizScore')
			->where('utilisateur_id = ?', $this->getId())
			->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_recrutements
		SET recrutement_id_utilisateur = NULL
		WHERE recrutement_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();

		$stmt = $dbh->prepare("
		UPDATE zcov2_recrutements_candidatures
		SET candidature_id_utilisateur = NULL
		WHERE candidature_id_utilisateur = :id");
		$stmt->bindValue(':id', $this->getId());
		$stmt->execute();
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addGetterConstraint('username', new Constraints\Username(array(
			'groups' => 'registration',
		)));
		$metadata->addGetterConstraint('rawPassword', new Constraints\Password(array(
			'groups' => array('registration', 'editPassword'),
		)));
		$metadata->addGetterConstraint('email', new Constraints\Email(array(
			'groups' => 'registration',
		)));
		/*$metadata->addGetterConstraint('nouvel_email', new Constraints\Email(array(
			'groups' => 'editEmail',
		)));*/
		$metadata->addConstraint(new Callback(array(
			'groups'  => 'absence',
            'methods' => array('isAbsenceValid'),
        )));
	}
}