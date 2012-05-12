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
namespace Zco\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Formulaire de réponse à une demande d'un nouveau nom d'utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AnswerNewUsernameType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('status', 'choice', array(
			'label' => 'Réponse',
			'choices' => array(
				CH_PSEUDO_ACCEPTE => 'Accepter',
				CH_PSEUDO_REFUSE => 'Refuser',
			),
		));
		$builder->add('adminResponse', 'textarea', array(
			'label' => 'Raison envoyée par courriel',
			'attr' => array('class' => 'textarea'),
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_user_answerNewUsername';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class'        => 'UserNewUsername',
			'validation_groups' => array('answer'),
		);
	}
}