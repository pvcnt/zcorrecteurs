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

namespace Zco\Bundle\Doctrine1Bundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the Unique Entity validator
 *
 * @Annotation
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class UniqueEntity extends Constraint
{
	public $message = 'This value is already used';
	public $service = 'zco_doctrine1.orm.validator.unique';
	public $fields = array();

	public function getRequiredOptions()
	{
		return array('fields');
	}

	/**
	 * The validator must be defined as a service with this name.
	 *
	 * @return string
	 */
	public function validatedBy()
	{
		return $this->service;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

	public function getDefaultOption()
	{
		return 'fields';
	}
}
