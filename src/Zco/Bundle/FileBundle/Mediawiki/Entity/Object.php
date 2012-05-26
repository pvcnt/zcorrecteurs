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

namespace Zco\Bundle\FileBundle\Mediawiki\Entity;

abstract class Object
{
    private $id;
    private $namespace;
    private $title;
    private $properties = array();
    
    public function __construct($id, $namespace, $title)
    {
        $this->id        = $id;
        $this->namespace = $namespace;
        $this->title     = $title;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getNormalizedTitle()
    {
        return substr(str_replace('_', ' ', $this->title), strpos($this->title, ':') + 1);
    }
    
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    public function getProperty($name, $key = null)
    {
        if (!array_key_exists($name, $this->properties))
        {
            throw new \InvalidArgumentException(sprintf(
                'Property "%s" is invalid.',
                $name
            ));
        }
        
        if (!$key)
        {
            return $this->properties[$name];
        }
        
        if (!array_key_exists($key, $this->properties[$name]))
        {
            throw new \InvalidArgumentException(sprintf(
                'Key "%s" for property "%s" is invalid.',
                $key, $name
            ));
        }
        
        return $this->properties[$name][$key];
    }
}