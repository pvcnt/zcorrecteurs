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

namespace Zco\Bundle\Doctrine1Bundle\Form;

use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

/**
 * Service en charge de « deviner » le type des champs de formulaire et les
 * options à leur associer en fonction du mapping des entités dans Doctrine.
 *
 * @author vincnet1870 <vincent@zcorrecteurs.fr>
 */
class DoctrineOrmTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * {@inheritDoc}
     */
    public function guessType($class, $property)
    {
        if (!$metadata = $this->getMetadata($class)) {
            return new TypeGuess('text', array(), Guess::LOW_CONFIDENCE);
        }

        if ($metadata->hasRelation($property)) {
            $multiple = $metadata->getRelation($property)->getType() === \Doctrine_Relation::MANY;
            $class = $metadata->getRelation($property)->getClass();

            return new TypeGuess('entity', array('class' => $class, 'multiple' => $multiple), Guess::HIGH_CONFIDENCE);
        }

        switch ($metadata->getTypeOf($property)) {
            case 'boolean':
                return new TypeGuess('checkbox', array(), Guess::HIGH_CONFIDENCE);
            case 'datetime':
            case 'timestamp':
                return new TypeGuess('datetime', array(), Guess::HIGH_CONFIDENCE);
            case 'date':
                return new TypeGuess('date', array(), Guess::HIGH_CONFIDENCE);
            case 'decimal':
            case 'float':
                return new TypeGuess('number', array(), Guess::MEDIUM_CONFIDENCE);
            case 'integer':
            case 'bigint':
            case 'smallint':
                return new TypeGuess('integer', array(), Guess::MEDIUM_CONFIDENCE);
            case 'string':
                $definition = $metadata->getDefinitionOf($property);
                if ($definition['length']) {
                    return new TypeGuess('text', array(), Guess::MEDIUM_CONFIDENCE);
                } else {
                    return new TypeGuess('textarea', array(), Guess::MEDIUM_CONFIDENCE);
                }
            case 'time':
                return new TypeGuess('time', array(), Guess::HIGH_CONFIDENCE);
            default:
                return new TypeGuess('text', array(), Guess::LOW_CONFIDENCE);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessRequired($class, $property)
    {
        $metadata = $this->getMetadata($class);
        if ($metadata && $metadata->hasField($property)) {
            $definition = $metadata->getDefinitionOf($property);
            if (isset($definition['notnull']) && $definition['notnull']) {
                return new ValueGuess(true, Guess::HIGH_CONFIDENCE);
            }
            return new ValueGuess(false, Guess::MEDIUM_CONFIDENCE);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function guessMaxLength($class, $property)
    {
        $metadata = $this->getMetadata($class);
        if ($metadata && $metadata->hasField($property) && !$metadata->hasRelation($property)) {
            $definition = $metadata->getDefinitionOf($property);
            if (isset($definition['length'])) {
                return new ValueGuess($definition['length'], Guess::HIGH_CONFIDENCE);
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function guessPattern($class, $property)
    {
        return null;
    }

    /**
     * Retourne la table Doctrine associée à une classe d'entité donnée.
     *
     * @param  string $class Nom de classe d'entité
     * @return \Doctrine_Table|false
     */
    private function getMetadata($class)
    {
        static $exists = array();

        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }
        if (!isset($exists[$class])) {
            $exists[$class] = \Doctrine_Core::isValidModelClass($class);
        }

        return ($exists[$class]) ? \Doctrine_Core::getTable($class) : false;
    }
}
