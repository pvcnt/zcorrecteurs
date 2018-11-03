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

namespace Zco\Bundle\PagesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zco\Bundle\PagesBundle\Entity\Contact;

class ContactType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('nom', TextType::class, array(
			'required' => false, 
			'label' => 'Votre nom',
		));
		$builder->add('courriel', EmailType::class, array(
			'label' => 'Adresse courriel', 
		));
		$builder->add('raison', ChoiceType::class, array(
			'empty_value' => 'Choisissez une raison', 
			'choices' => Contact::getChoices(),
		));
		$builder->add('sujet', TextType::class, array(
			'attr' => array('class' => 'input-xxlarge'),
		));
		$builder->add('message', TextareaType::class, array(
			'label' => 'Votre message',
			'attr' => array('class' => 'input-xxlarge', 'rows' => '10'),
		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
        $resolver->setDefaults([
			'data_class' => Contact::class,
		]);
	}
}