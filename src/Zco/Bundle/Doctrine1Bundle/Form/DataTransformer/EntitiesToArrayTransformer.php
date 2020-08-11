<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

class EntitiesToArrayTransformer implements DataTransformerInterface
{
	private $choiceList;

	public function __construct(EntityChoiceList $choiceList)
	{
		$this->choiceList = $choiceList;
	}

	/**
	 * Transforms entities into choice keys.
	 *
	 * @param  \Doctrine_Collection $collection A collection of entities or NULL
	 * @return mixed An array of choice keys, a single key or NULL
	 */
	public function transform(\Doctrine_Collection $collection = null)
	{
		if (null === $collection)
		{
			return array();
		}

		$array = array();

		if (count($this->choiceList->getIdentifier()) > 1)
		{
			// load all choices
			$availableEntities = $this->choiceList->getEntities();

			foreach ($collection as $entity)
			{
				// identify choices by their collection key
				$key = array_search($entity, $availableEntities, true);
				$array[] = $key;
			}
		}
		else
		{
			foreach ($collection as $entity)
			{
				$value = current($this->choiceList->getIdentifierValues($entity));
				$array[] = is_numeric($value) ? (int) $value : $value;
			}
		}

		return $array;
	}

	/**
	 * Transforms choice keys into entities.
	 *
	 * @param  mixed $keys An array of keys, a single key or NULL
	 * @return \Doctrine_Collection
	 */
	public function reverseTransform($keys)
	{
		$collection = new \Doctrine_Collection();

		if ('' === $keys || null === $keys)
		{
			return $collection;
		}

		if (!is_array($keys))
		{
			throw new UnexpectedTypeException($keys, 'array');
		}

		$entities = $this->choiceList->getEntitiesByKeys($keys);
		if (count($keys) !== count($entities))
		{
			throw new TransformationFailedException('Not all entities matching the keys were found.');
		}

		foreach ($entities as $entity)
		{
			$collection->add($entity);
		}

		return $collection;
	}
}
