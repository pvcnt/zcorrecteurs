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

namespace Zco\Bundle\OptionsBundle\Form\Type;

use Zco\Bundle\UserBundle\Form\EventListener\CalculateCoordinatesSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Geocoder\GeocoderInterface;

/**
 * Formulaire permettant de modifier le profil d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditProfileType extends AbstractType
{
	protected $geocoder;

	/**
	 * Constructeur.
	 *
	 * @param GeocoderInterface $geocoder
	 */
	public function __construct(GeocoderInterface $geocoder)
	{
		$this->geocoder = $geocoder;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->addEventSubscriber(new CalculateCoordinatesSubscriber($this->geocoder));
		$builder->add('twitter', null, array(
			'label' => 'Compte Twitter',
			'required' => false,
		));
		$builder->add('email_displayed', null, array(
			'label' => 'Autoriser les autres utilisateurs à m\'envoyer un courriel', 
			'required' => false,
		));
		$builder->add('display_signature', null, array(
			'label' => 'Afficher ma signature en-dessous de ma présentation personnelle', 
			'required' => false,
		));
		$builder->add('country_displayed', null, array(
			'label' => 'Afficher le pays dans lequel je me trouve', 
			'required' => false,
		));
		$builder->add('address', null, array(
			'label' => 'Adresse ou ville', 
			'required' => false,
		));
		$builder->add('job', null, array(
			'label' => 'Profession ou études', 
			'required' => false,
		));
		$builder->add('hobbies', null, array(
			'label' => 'Passions, loisirs', 
			'required' => false,
		));
		$builder->add('birth_date', null, array(
			'label' => 'Date de naissance', 
			'required' => false,
			'input' => 'string', 
			'widget' => 'single_text',
			'format' => 'Y-MM-dd',
		));
		$builder->add('website', 'url', array(
			'label' => 'Site web', 
			'required' => false,
		));
		$builder->add('sexe', 'choice', array(
			'label' => 'Sexe', 
			'required' => true,
			'choices' => array(
				0 => 'Non spécifié',
				SEXE_MASCULIN => 'Masculin',
				SEXE_FEMININ => 'Féminin',
			),
		));
		$builder->add('citation', null, array(
			'label' => 'Citation', 
			'required' => false,
		));
		$builder->add('signature', 'zform', array(
			'label' => 'Signature', 
			'required' => false,
		));
		$builder->add('biography', 'zform', array(
			'label' => 'Présentation personnelle', 
			'required' => false,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_options_editProfile';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class'        => 'Utilisateur',
			'validation_groups' => array('Default'),
		);
	}
}