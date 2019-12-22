<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

use Zco\Bundle\UserBundle\Form\EventListener\AddAutoValidateFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Formulaire de demande d'un nouveau nom d'utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class NewUsernameType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('newUsername', null, array(
			'label' => 'Nouveau pseudonyme',
			'required' => true,
		));
		$builder->add('reason', 'zform', array(
			'label' => 'Raison du changement',
		));
		
		$subscriber = new AddAutoValidateFieldSubscriber($builder->getFormFactory());
		$builder->addEventSubscriber($subscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_user_newUsername';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class'        => 'UserNewUsername',
			'validation_groups' => array('create'),
		);
	}
}