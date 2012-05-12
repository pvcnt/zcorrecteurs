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

namespace Zco\Bundle\CoreBundle\View;

abstract class View implements ViewInterface
{
    protected $children = array();
    
    public function addchild($child)
    {
        $this->children[] = $child;
    }
    
    public function renderChildren()
    {
        return $this->renderViews($this->children());
    }
    
    public function renderViews(array $views)
    {
        $rendered = array();
        foreach ($views as $view)
        {
            $rendered[] = $this->renderView($view);
        }
        
        return implode($rendered);
    }
    
    public function renderView($view)
    {
        if ($view instanceof ViewInterface)
        {
            return $view->render();
        }
        elseif (is_array($view))
        {
            return $this->renderViews($view);
        }
        
        return (string) $view;
    }
    
    public function getName()
    {
        return $this->underscore(get_class($this));
    }
    
    private function underscore($str)
    {
        $str = str_replace('::', '/', $str);
        $str = preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), $str);
        
        return strtolower($str);
    }
}
