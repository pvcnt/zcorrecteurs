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

namespace Zco\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création d'un nouveau compte utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CreateUserType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', TextType::class, array(
			'label' => 'Pseudonyme',
		));
		$builder->add('email', EmailType::class, array(
			'label' => 'Adresse courriel', 
			'required' => true,
		));
		$builder->add('rawPassword', RepeatedType::class, array(
			'type'  => PasswordType::class,
			'first_options' => ['label' => 'Mot de passe'],
			'second_options' => ['label' => 'Confirmez le mot de passe'],
			'invalid_message' => 'Saisissez deux fois le même mot de passe.',
		));
	}

	/**
	 * {@inheritdoc}
	 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
			'data_class'        => \Utilisateur::class,
			'validation_groups' => ['registration'],
		]);
	}
}