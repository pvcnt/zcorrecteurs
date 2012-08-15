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
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validateur abstrait s'occupant de données liées à l'utilisateur et dont 
 * la validation est prise en charge par une méthode de la classe User.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserPasswordValidator extends ConstraintValidator
{
    protected $user;
    
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
     * Vérifie que le mot de passe soit valide.
     *
     * @param  mixed value
     * @param  Constraint $constraint
     * @return boolean
     *
     * @throws UnexpectedTypeException si la valeur n'est pas une chaîne de caractères
     */
    public function isValid($value, Constraint $constraint)
    {
        if (!is_string($value))
        {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->user->checkPassword($value))
        {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }
}