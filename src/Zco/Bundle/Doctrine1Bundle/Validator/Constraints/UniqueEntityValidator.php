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

namespace Zco\Bundle\Doctrine1Bundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Unique Entity Validator checks if one or a set of fields contain unique values.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class UniqueEntityValidator extends ConstraintValidator
{
	/**
	 * @param object $entity
	 * @param Constraint $constraint
	 *
	 * @return bool
	 */
	public function isValid($entity, Constraint $constraint)
	{
		if (!is_array($constraint->fields) && !is_string($constraint->fields))
		{
			throw new UnexpectedTypeException($constraint->fields, 'array');
		}

		$fields = (array) $constraint->fields;

		if (0 === count($fields))
		{
			throw new ConstraintDefinitionException('At least one field has to be specified.');
		}
		
		$className = $this->context->getCurrentClass();
		$class = \Doctrine_Core::getTable($className);

		$criteria = array();
		foreach ($fields as $fieldName)
		{
			if (!$class->hasField($fieldName) && !$class->hasRelation($fieldName))
			{
				throw new ConstraintDefinitionException("Only field names mapped by Doctrine can be validated for uniqueness.");
			}

			$criteria[$fieldName] = $entity->get($fieldName);

			if (null === $criteria[$fieldName])
			{
				return true;
			}

			if ($class->hasRelation($fieldName))
			{
				$relatedClass = $metadata->getRelation($property)->getClass();
				$relatedId = $relatedClass->getIdentifierValues($criteria[$fieldName]);

				if (count($relatedId) > 1)
				{
					throw new ConstraintDefinitionException(
						"Associated entities are not allowed to have more than one identifier field to be " .
						"part of a unique constraint in: " . $class->getName() . "#" . $fieldName
					);
				}
				$criteria[$fieldName] = array_pop($relatedId);
			}
		}

		$repository = $em->getRepository($className);
		$result = $repository->findBy($criteria);

		/* If no entity matched the query criteria or a single entity matched,
		 * which is the same as the entity being validated, the criteria is
		 * unique.
		 */
		if (0 === count($result) || (1 === count($result) && $entity === $result[0]))
		{
			return true;
		}

		$oldPath = $this->context->getPropertyPath();
		$this->context->setPropertyPath( empty($oldPath) ? $fields[0] : $oldPath.'.'.$fields[0]);
		$this->context->addViolation($constraint->message, array(), $criteria[$fields[0]]);
		$this->context->setPropertyPath($oldPath);

		return true; // all true, we added the violation already!
	}
}
