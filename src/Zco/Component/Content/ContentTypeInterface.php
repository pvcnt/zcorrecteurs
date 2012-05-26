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

namespace Zco\Component\Content;

interface ContentTypeInterface
{
	/**
	 * Returns the name of the content type, i.e. the name of the class
	 * from which the records are inheriting.
	 *
	 * @return string
	 */
	function getName();
	
	/**
	 * Returns the human-readable name of the content type.
	 *
	 * @return string
	 */
	function getTitle();
	
	/**
	 * Returns the field associated with the type by its name.
	 * 
	 * @param string $key The field's name.
	 * @return Zco\Component\Content\Field\FieldInterface
	 */
	function get($key);
	
	function has($key);
	
	function getAll();
	
	function getFormatter($mode = 'default');
}