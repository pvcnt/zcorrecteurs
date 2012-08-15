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

/**
 * Formulaire permettant de modifier le profil d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditAbsenceType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('absence_start_date', 'date', array(
			'label' => 'Absent à partir du',
			'input' => 'string', 
			'widget' => 'single_text',
			'format' => 'Y-MM-dd',
		));
		$builder->add('absence_end_date', null, array(
			'label' => 'Absent jusqu\'au', 
			'required' => false,
			'input' => 'string', 
			'widget' => 'single_text',
			'format' => 'Y-MM-dd',
		));
		$builder->add('absence_reason', 'zform', array(
			'label' => 'Raison de mon absence', 
			'required' => false,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_options_editAbsence';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class'        => 'Utilisateur',
			'validation_groups' => array('absence'),
		);
	}
}