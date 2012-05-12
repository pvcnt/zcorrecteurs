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

namespace Zco\Component\Content\Field;

abstract class Field implements FieldInterface
{
	protected $name;
	protected $title;
	protected $description = '';
	protected $cardinality = 1;
	protected $required = false;
	protected $editable = true;
	protected $options = array();
	
	public function __construct($name)
	{
		$this->name = $name;
		$this->title = ucfirst(str_replace('_', ' ', $name));
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setDescription($description)
	{
		$this->description = $description;
		return $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setCardinality($cardinality)
	{
		$this->cardinality = $cardinality;
		return $this;
	}
	
	public function getCardinality()
	{
		return $this->cardinality;
	}
	
	public function setRequired($required = true)
	{
		$this->required = $required;
		return $this;
	}
	
	public function isRequired()
	{
		return $this->required;
	}
	
	public function setEditable($editable = true)
	{
		$this->editable = $editable;
		return $this;
	}
	
	public function isEditable()
	{
		return $this->editable;
	}
	
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
		return $this;
	}
	
	public function getOption($key)
	{
		return isset($this->options[$key]) ? $this->options[$key] : null;
	}
}