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

namespace Zco\Bundle\UserBundle\Validator\Constraints;

use Zco\Bundle\UserBundle\Exception\ValueException;
use Zco\Bundle\UserBundle\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validateur abstrait s'occupant de données liées à l'utilisateur et dont 
 * la validation est prise en charge par une méthode de la classe User.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class AbstractValidator extends ConstraintValidator
{
	private $user;
	
	/**
	 * Constructeur.
	 *
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * Fonction d'aide validant une valeur par un appel à la méthode appropriée 
	 * de la classe User.
	 *
	 * @param  mixed $value La valeur à valider
	 * @param  string $method La méthode à utiliser pour valider
	 * @param  Constraint $constraint La contrainte validée
	 * @return boolean Valeur validée ?
	 */
	protected function doValidate($value, $method, Constraint $constraint)
	{
		//Les méthodes renvoie vrai lorsque la validation réussit et une 
		//exception de type ValueException lorsqu'elle échoue.
		try
		{
			if ($this->user->$method($value))
			{
				return true;
			}
			
			//Cas où la validation pourrait renvoyer false sans exception, 
			//on utilise le message par défaut.
			$this->context->addViolation($constraint->message);
		}
		catch (ValueException $e)
		{
			//Si on a une exception, celle-ci transporte généralement le 
			//message détaillant l'erreur en question.
			$this->context->addViolation($e->getMessage() ?: $constraint->message);
		}
		
		return false;
	}
}