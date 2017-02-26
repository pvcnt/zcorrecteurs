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

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zco\Bundle\Doctrine1Bundle\Form\DataTransformer;

use Zco\Bundle\Doctrine1Bundle\Form\ChoiceList\EntityChoiceList;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    private $choiceList;

    public function __construct(EntityChoiceList $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * Transforms entities into choice keys.
     *
     * @param  \Doctrine_Record $entity A single entity or NULL
     * @return mixed An array of choice keys, a single key or NULL
     */
    public function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return '';
        }

        if (!$entity instanceof \Doctrine_Record) {
            throw new UnexpectedTypeException($entity, '\Doctrine_Record');
        }

        if (count($this->choiceList->getIdentifier()) > 1) {
            // load all choices
            $availableEntities = $this->choiceList->getEntities();

            return array_search($entity, $availableEntities);
        }

        return current($this->choiceList->getIdentifierValues($entity));
    }

    /**
     * Transforms choice keys into entities.
     *
     * @param  mixed $key An array of keys, a single key or NULL
     * @return \Doctrine_Record  A single entity or NULL
     */
    public function reverseTransform($key)
    {
        if ('' === $key || null === $key) {
            return null;
        }

        if (count($this->choiceList->getIdentifier()) > 1 && !is_numeric($key)) {
            throw new UnexpectedTypeException($key, 'numeric');
        }

        if (!($entities = $this->choiceList->getEntitiesByKeys(array($key)))) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
        }

        return $entities[0];
    }
}
