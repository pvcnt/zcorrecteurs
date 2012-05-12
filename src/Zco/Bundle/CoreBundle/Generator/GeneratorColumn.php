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

namespace Zco\Bundle\CoreBundle\Generator;

class GeneratorColumn implements \ArrayAccess
{
	protected
		$foreignModel = null,
		$name = null,
		$table = null,
		$properties = array();

	public function __construct($name, \Doctrine_Table $table)
	{
		$this->name = $name;
		$this->table = $table;
		$this->properties = $table->getDefinitionOf($name);
	}

	/**
	 * Get the name of the column.
	 * @return unknown_type
	 */
	public function getName()
	{
		return $this->table->getColumnName($this->name);
	}

	/**
	 * Get the name of the field (different of the name of the column
	 * in the case of an alias).
	 * @return unknown_type
	 */
	public function getFieldName()
	{
		return $this->table->getFieldName($this->getName());
	}

	public function getType()
	{
		return isset($this->properties['type']) ? strtolower($this->properties['type']) : null;
	}

	public function getLength()
	{
		return $this->properties['length'];
	}

	public function isRequired()
	{
		if (isset($this->properties['notnull']))
		{
			return $this->properties['notnull'];
		}
		elseif (isset($this->properties['notblank']))
		{
			return $this->properties['notblank'];
		}
		else
		{
			return false;
		}
	}

	public function isPrimaryKey()
	{
		return isset($this->properties['primary']) ? $this->properties['primary'] : false;
	}

	public function isForeignKey()
	{
		if (isset($this->foreignModel))
		{
			return true;
		}
		if ($this->isPrimaryKey())
		{
			return false;
		}

		foreach ($this->table->getRelations() as $relation)
		{
			if (strtolower($relation->getLocal()) == strtolower($this->name))
			{
				$this->foreignModel = $relation->getClass();
				return true;
			}
		}
		return false;
	}

	public function getForeignModel()
	{
		return $this->isForeignKey() ? $this->foreignModel : false;
	}

	public function getForeignTable()
	{
		return $this->isForeignKey() ? \Doctrine_Core::getTable($this->foreignModel) : false;
	}

	public function isEditable()
	{
		return !$this->isPrimaryKey();
	}

	public function getTable()
	{
		return $this->table;
	}

	public function offsetGet($offset)
	{
		return isset($this->properties[$offset]) ? $this->properties[$offset] : null;
	}

	public function offsetExists($offset)
	{
		return isset($this->properties[$offset]);
	}

	public function offsetUnset($offset)
	{
		unset($this->properties[$offset]);
	}

	public function offsetSet($offset, $value)
	{
		$this->properties[$offset] = $value;
	}
}