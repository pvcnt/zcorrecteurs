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

namespace Zco\Bundle\FileBundle\Mediawiki\Request;

class Query implements RequestInterface
{
    private $request = array();
    
    private static $lists = array(
        'allcategories' => 'ac',
        'allimages' => 'ai',
        'allpages'  => 'ap',
    );
    
    private static $properties = array(
        'info' => 'in',
        'revisions' => 'rv',
        'categories' => 'cl',
        'imageinfo' => 'ii',
        'langlinks' => 'll',
        'links' => 'pl',
        'templates' => 'tl',
        'images' => 'im',
        'extlinks' => 'el',
        'categoryinfo' => 'ci',
        'duplicatefiles' => 'df',
        'pageprops' => 'pp',
    );
    
    public function useGenerator($generator, array $options = array())
    {
        if (!isset(self::$lists[$generator]))
        {
            throw new \InvalidArgumentException(sprintf(
                'Unknown generator "%s".',
                $generator
            ));
        }
        if (!empty($this->request['generator']))
        {
            throw new \InvalidArgumentException(sprintf(
                'Impossible to use two generators ("%s" is already set).',
                $generator
            ));
        }
        
        $prefix = 'g'.self::$lists[$generator];
        $this->request['generator'] = $generator;
        foreach ($options as $key => $value)
        {
            $this->request[$prefix.$key] = $value;
        }
        
        return $this;
    }
    
    public function useProperty($property, array $options = array())
    {
        if (!isset(self::$properties[$property]))
        {
            throw new \InvalidArgumentException(sprintf(
                'Unknown property "%s".',
                $property
            ));
        }
        if (!empty($this->request['prop']) && in_array($property, $this->request['prop']))
        {
            throw new \InvalidArgumentException(sprintf(
                'Impossible to reconfigure a property ("%s" is already set).',
                $property
            ));
        }
        
        $prefix = self::$properties[$property];
        $this->request['prop'][] = $property;
        foreach ($options as $key => $value)
        {
            $this->request[$prefix.$key] = is_array($value) ? implode('|', $value) : $value;
        }
        
        return $this;
    }
    
    public function getRequest()
    {
        $this->request['action'] = 'query';
        sort($this->request['prop']);
        $this->request['prop']   = implode('|', $this->request['prop']);
        
        return $this->request;
    }
}