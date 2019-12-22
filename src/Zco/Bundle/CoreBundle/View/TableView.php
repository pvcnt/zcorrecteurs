<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\CoreBundle\View;

class TableView extends View
{
    protected $data;
    protected $headers;
    protected $rowClasses = array();
    protected $columnClasses = array();
    protected $zebraStripes = true;
    protected $noDataString;
    protected $className;
    protected $columnVisibility = array();

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        
        return $this;
    }

    public function setColumnClasses(array $columnClasses)
    {
        $this->columnClasses = $columnClasses;
        
        return $this;
    }

    public function setRowClasses(array $rowClasses)
    {
        $this->rowClasses = $rowClasses;
        
        return $this;
    }

    public function setNoDataString($noDataString)
    {
        $this->noDataString = (string) $noDataString;
        
        return $this;
    }

    public function setClassName($className)
    {
        $this->className = $className;
        
        return $this;
    }

    public function setZebraStripes($zebraStripes)
    {
        $this->zebraStripes = (boolean) $zebraStripes;
        
        return $this;
    }

    public function setColumnVisibility(array $visibility)
    {
        $this->columnVisibility = $visibility;
        
        return $this;
    }

    public function render(array $options = array())
    {
        $class = $this->className;
        if ($class !== null)
        {
            $class = ' class="UI_items '.$class.'"';
        } else
        {
            $class = ' class="UI_items"';
        }
        $table = array('<table'.$class.'>');

        $colClasses = array();
        foreach ($this->columnClasses as $key => $class)
        {
            if (strlen($class))
            {
                $colClasses[] = ' class="'.$class.'"';
            }
            else
            {
                $colClasses[] = null;
            }
        }

        //Effectue le rendu de tous les en-têtes de colonnes.
        $visibility = array_values($this->columnVisibility);
        $headers = $this->headers;
        if ($headers)
        {
            while (count($headers) > count($visibility))
            {
                $visibility[] = true;
            }
            $table[] = '<thead>';
            $table[] = '<tr>';
            foreach ($headers as $i => $header)
            {
                if (!$visibility[$i])
                {
                    continue;
                }
                $table[] = '<th'.(isset($colClasses[$i]) ? $colClasses[$i] : '').'>'.$header.'</th>';
            }
            $table[] = '</tr>';
            $table[] = '</thead>';
        }

        $table[] = '<tbody>';
        $data = $this->data;
        if ($data)
        {
            $rowNum = 0;
            foreach ($data as $row)
            {
                while (count($row) > count($colClasses))
                {
                    $colClasses[] = null;
                }
                while (count($row) > count($visibility))
                {
                    $visibility[] = true;
                }
                $class = isset($this->rowClasses[$rowNum]) ? $this->rowClasses[$rowNum] : '';
                if ($this->zebraStripes && ($rowNum % 2))
                {
                    $class = ($class) ? 'alt alt-'.$class : 'alt';
                }
                if ($class)
                {
                    $class = ' class="'.$class.'"';
                }
                $table[] = '<tr'.$class.'>';
                
                // NOTE: Use of a separate column counter is to allow this to work
                // correctly if the row data has string or non-sequential keys.
                $colNum = 0;
                foreach ($row as $value)
                {
                    if (!$visibility[$colNum])
                    {
                        ++$colNum;
                        continue;
                    }
                    $class = $colClasses[$colNum];
                    if ($class)
                    {
                        $table[] = '<td'.$class.'>';
                    }
                    else
                    {
                        $table[] = '<td>';
                    }
                    $table[] = $this->renderView($value).'</td>';
                    ++$colNum;
                }
                ++$rowNum;
            }
        }
        else
        {
            $colspan = max(count($headers), 1);
            $table[] =
                '<tr class="no-data"><td colspan="'.$colspan.'">'.
                    coalesce($this->noDataString, 'Aucune donnée disponible.').
                '</td></tr>';
        }
        $table[] = '</tbody>';
        $table[] = '</table>';
        
        return implode('', $table);
    }
}

