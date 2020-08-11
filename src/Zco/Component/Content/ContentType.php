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

namespace Zco\Component\Content;

class ContentType implements ContentTypeInterface
{
	protected $name;
	protected $title;
	protected $fields = array();
	protected $formatters = array();
	
	public function __construct($name)
	{
		$this->name = $name;
		$this->title = ucfirst(strtolower($name));
		$this->configure();
	}
	
	protected function configure()
	{
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	public function add(FieldInterface $field)
	{
		if (isset($this->fields[$field->getName()]))
		{
			throw new InvalidArgumentException('Field "'.$field->getName().'" is already defined in content type "'.$this->name.'".')
		}
		$this->fields[$field->getName()] = $field;
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get($key)
	{
		return isset($this->fields[$key]) ? $this->fields[$key] : null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function has($key)
	{
		return isset($this->fields[$key]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAll()
	{
		return $this->fields;
	}
	
	public function getFormatter($mode = 'default')
	{
		return isset($this->formatters[$mode]) ? $this->formatters[$mode] : null;
	}
	
	public function addFormatter($mode, FormatterInterface $formatter)
	{
		if (isset($this->formatters[$mode]))
		{
			throw new InvalidArgumentException('Formatter for mode "'.$mode.'" is already defined in content type "'.$this->name.'".')
		}
		$this->formatters[$mode] = $formatter;
		return $this;
	}
}