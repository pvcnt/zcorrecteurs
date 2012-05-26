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
namespace Zco\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Adresses courriel bannies.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BannedEmailType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('email', null, array(
			'label' => 'Plage à bannir',
		));
		$builder->add('reason', 'zform', array(
			'label'  => 'Raison visible par les administrateurs',
			'required' => false,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'zco_user_bannedEmail';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'BannedEmail',
		);
	}
}