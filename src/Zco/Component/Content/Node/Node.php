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

namespace Zco\Component\Content\Node;

class Node implements NodeInterface
{
	protected $record;
	protected $contentType;
	protected $options = array();
	
	public function __construct($contentType, $record, array $options = array())
	{
		$this->record = $record;
		$this->contentType = $contentType;
		$this->options = array_merge(array('id_column' => 'id', 'title_column' => 'title'), $options);
	}
	
	public function getId()
	{
		return $this->record->get($this->options['id_column']);
	}
	
	public function getTitle()
	{
		return $this->record->get($this->options['title_column']);
	}
	
	public function getUri()
	{
		return '';
	}
	
	public function getValue($key)
	{
		return $this->record->get($key);
	}
	
	public function getValues()
	{
		$values = array();
		foreach (array_keys($this->contentType->getAll()) as $key)
		{
			$values[$key] = $this->record->get($key);
		}
		
		return $values;
	}
	
	public function getField($key)
	{
		return isset($this->contentType->has($key)) ? $this->contentType->get($key) : null;
	}
	
	public function getFields()
	{
		return $this->contentType->getAll();
	}
	
	function getContentType()
	{
		return $this->contentType;
	}
	
	public function getRecord()
	{
		return $this->record;
	}
	
	public function render($mode = 'default')
	{
		return $this->contentType->getFormatter($mode)->render($this);
	}
}