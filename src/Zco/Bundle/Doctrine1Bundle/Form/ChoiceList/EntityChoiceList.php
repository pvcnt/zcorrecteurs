<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\Doctrine1Bundle\Form\ChoiceList;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;

class EntityChoiceList extends ArrayChoiceList
{
    private $class;
    private $entities = array();
    private $query;
    private $identifier = array();
    private $propertyPath;

	/**
	 * Constructor.
	 *
	 * @param string $class	The class name
	 * @param string $property The property name
	 * @param \Doctrine_Query|\Closure $query An optional query builder
	 * @param array|\Closure $choices An array of choices or a function returning an array
	 */
	public function __construct($class, $property = null, $query, $choices = null)
	{
		// If a query builder was passed, it must be a closure or \Doctrine_Query instance.
		if (!(null === $query || $query instanceof \Doctrine_Query || $query instanceof \Closure))
		{
			throw new UnexpectedTypeException($queryBuilder, '\Doctrine_Query or \Closure');
		}

		if ($query instanceof \Closure)
		{
            $query = $query(\Doctrine_Core::getTable($class));
			if (!$query instanceof Doctrine_Query)
			{
                throw new UnexpectedTypeException($query, '\Doctrine_Query');
            }
        }

		$this->class = $class;
		$this->query = $query;
		$this->identifier = (array) \Doctrine_Core::getTable($class)->getIdentifier();

		// The property option defines, which property (path) is used for
		// displaying entities as strings
		if ($property)
		{
			$this->propertyPath = new PropertyPath($property);
		}
		elseif (!method_exists($class, '__toString'))
		{
			// Otherwise expect a __toString() method in the entity
			throw new FormException('Entities passed to the choice field must have a "__toString()" method defined (or you can also override the "property" option).');
		}

		if (!is_array($choices) && !$choices instanceof \Closure && !is_null($choices))
		{
			throw new UnexpectedTypeException($choices, 'array or \Closure or null');
		}

		$this->choices = $choices;
	}

	/**
	 * Initializes the choices and returns them.
	 *
	 * If the entities were passed in the "choices" option, this method
	 * does not have any significant overhead. Otherwise, if a query builder
	 * was passed in the "query_builder" option, this builder is now used
	 * to construct a query which is executed. In the last case, all entities
	 * for the underlying class are fetched from the repository.
	 *
	 * @return array  An array of choices
	 */
	protected function load()
	{
		parent::load();

		if (is_array($this->choices))
		{
			$entities = $this->choices;
		}
		elseif ($this->query)
		{
			$entities = $this->query->execute();
		}
		else
		{
			$entities = \Doctrine_Core::getTable($this->class)->findAll();
		}
		
		$this->choices = array();
		$this->entities = array();

		$this->loadEntities($entities);

		return $this->choices;
	}
	
	/**
	 * Converts entities into choices with support for groups.
	 *
	 * The choices are generated from the entities. If the entities have a
	 * composite identifier, the choices are indexed using ascending integers.
	 * Otherwise the identifiers are used as indices.
	 *
	 * If the option "property" was passed, the property path in that option
	 * is used as option values. Otherwise this method tries to convert
	 * objects to strings using __toString().
	 *
	 * @param array  $entities An array of entities
	 * @param string $group	A group name
	 */
	private function loadEntities($entities, $group = null)
	{
		foreach ($entities as $key => $entity)
		{
			if (is_array($entity))
			{
				// Entities are in named groups
				$this->loadEntities($entity, $key);
				continue;
			}

			if ($this->propertyPath)
			{
				// If the property option was given, use it
				$value = $this->propertyPath->getValue($entity);
			}
			else
			{
				$value = (string) $entity;
			}

			if (count($this->identifier) > 1)
			{
				// When the identifier consists of multiple field, use
				// naturally ordered keys to refer to the choices
				$id = $key;
			}
			else
			{
				// When the identifier is a single field, index choices by
				// entity ID for performance reasons
				$id = current($this->getIdentifierValues($entity));
			}

			if (null === $group)
			{
				// Flat list of choices
				$this->choices[$id] = $value;
			}
			else
			{
				// Nested choices
				$this->choices[$group][$id] = $value;
			}

			$this->entities[$id] = $entity;
		}
	}
	
	/**
     * Returns the fields of which the identifier of the underlying class consists.
     *
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the according entities for the choices.
     *
     * If the choices were not initialized, they are initialized now. This
     * is an expensive operation, except if the entities were passed in the
     * "choices" option.
     *
     * @return array  An array of entities
     */
    public function getEntities()
    {
        if (!$this->loaded)
		{
            $this->load();
        }

        return $this->entities;
    }

    /**
     * Returns the entities for the given keys.
     *
     * If the underlying entities have composite identifiers, the choices
     * are initialized. The key is expected to be the index in the choices
     * array in this case.
     *
     * If they have single identifiers, they are either fetched from the
     * internal entity cache (if filled) or loaded from the database.
     *
     * @param  array $keys  The choice key (for entities with composite
     *                      identifiers) or entity ID (for entities with single
     *                      identifiers)
     * @return object[]     The matching entity
     */
    public function getEntitiesByKeys(array $keys)
    {
        if (!$this->loaded)
		{
            $this->load();
        }

        $found = array();

        foreach ($keys as $key)
		{
            if (isset($this->entities[$key]))
			{
                $found[] = $this->entities[$key];
            }
        }
		
        return $found;
    }

	/**
	 * Returns the values of the identifier fields of an entity.
	 *
	 * Doctrine must know about this entity, that is, the entity must already
	 * be persisted or added to the identity map before. Otherwise an
	 * exception is thrown.
	 *
	 * @param  object $entity The entity for which to get the identifier
	 *
	 * @return array		  The identifier values
	 *
	 * @throws FormException  If the entity does not exist in Doctrine's identity map
	 */
	public function getIdentifierValues($entity)
	{
		if (!$entity instanceof \Doctrine_Record)
		{
			throw new FormException('Entities passed to the choice field must be instances of \Doctrine_Record');
		}

		$values = array();
		foreach ($this->identifier as $identifier)
		{
			$values[$identifier] = $entity->get($identifier);
		}
		
		return $values;
	}
}
