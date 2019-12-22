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

use Zco\Bundle\UserBundle\Form\EventListener\AddUserFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Formulaire de modification du niveau d'avertissement d'un membre.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class WarningType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('link', null, array(
			'label' => 'Lien du litige', 
		));
		$builder->add('percentage', 'integer', array(
			'label'  => 'Pourcentage',
//			'help'   => 'Une valeur négative aura pour effet de diminuer le pourcentage du membre',
		));
		$builder->add('reason', 'zform', array(
			'label'  => 'Raison donnée au membre',
			'required' => false,
//			'help' => 'Si le champ est laissé vide, aucun message ne sera envoyé au membre.',
		));
		$builder->add('admin_reason', 'zform', array(
			'label'  => 'Raison visible par les admins',
		));
		
		$subscriber = new AddUserFieldSubscriber($builder->getFormFactory());
		$builder->addEventSubscriber($subscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_user_warning';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'UserWarning',
		);
	}
}