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

namespace Zco\Bundle\AboutBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Représente une demande de contact envoyée par un visiteur. N'est pas 
 * sauvegardé en base de données.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Contact
{
	public $pseudo;
	public $id;
	public $nom;
	public $courriel;
	public $raison;
	public $sujet;
	public $message;
	
	private static $choices = array(
		'Inscription' => 'Inscription, désinscription, connexion',
		'Partenariat' => 'Partenariat et échange de visibilité',
		'Don' => 'Dons et mécénat',
		'Association' => 'Question sur l\'association Corrigraphie',
		'Question' => 'Question sur notre service',
		'Bénévolat' => 'Bénévolat au sein de l\'équipe du site',
		'Anomalie' => 'Anomalie importante, sécurité',
		'Autre' => 'Autre',
	);
	
	/**
	 * Constructeur.
	 *
	 * @param string $raison Raison de la demande
	 */
	public function __construct($raison)
	{
		if (isset(self::$choices[$raison]))
		{
			$this->raison = $raison;
		}
	}
	
	/**
	 * Retourne la liste des raisons de contact possibles.
	 *
	 * @return array
	 */
	public static function getChoices()
	{
		return self::$choices;
	}
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addPropertyConstraint('courriel', new NotBlank());
		$metadata->addPropertyConstraint('courriel', new Email());
		$metadata->addPropertyConstraint('sujet', new NotBlank());
		$metadata->addPropertyConstraint('raison', new Choice(array('choices' => array_keys(self::$choices))));
		$metadata->addPropertyConstraint('message', new NotBlank());
	}
}