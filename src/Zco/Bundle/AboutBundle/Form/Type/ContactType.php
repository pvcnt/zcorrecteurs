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
namespace Zco\Bundle\AboutBundle\Form\Type;

use Zco\Bundle\AboutBundle\Entity\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContactType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('nom', 'text', array(
			'required' => false, 
			'label' => 'Votre nom',
		));
		$builder->add('courriel', 'email', array(
			'label' => 'Adresse courriel', 
		));
		$builder->add('raison', 'choice', array(
			'empty_value' => 'Choisissez une raison', 
			'choices' => Contact::getChoices(),
		));
		$builder->add('sujet', 'text', array(
			'attr' => array('class' => 'input-xxlarge'),
		));
		$builder->add('message', 'textarea', array(
			'label' => 'Votre message',
			'attr' => array('class' => 'input-xxlarge', 'rows' => '10'),
		));
	}

	public function getName()
	{
		return 'zco_apropos_contact';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Zco\Bundle\AboutBundle\Entity\Contact',
		);
	}
}