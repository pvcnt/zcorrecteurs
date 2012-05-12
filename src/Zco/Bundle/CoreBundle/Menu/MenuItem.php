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

namespace Zco\Bundle\CoreBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem as BaseMenuItem;

/**
 * Implémentation prenant en compte les spécificités de l'interface du site 
 * qui ajoute un certain nombre de propriétés réutilisées par les différents 
 * moteurs de rendu.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MenuItem extends BaseMenuItem
{
	protected $html = array();
	protected $weight = 0;
	protected $separator = false;
	protected $credentials = array();
	protected $count;
	
	/**
     * {@inheritdoc}
     */
    public function getChild($name)
    {
        if (!isset($this->children[$name]))
		{
			$this->addChild($this->factory->createItem($name));
		}
		
		return $this->children[$name];
    }
    
    /**
     * Vérifie l'existence d'un sous-menu.
     *
     * @param  string $name Le nom du sous-menu
     * @return boolean
     */
    public function hasChild($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Alias de getParent(), plus intuitif lors de la manipulation de menus 
     * imbriqués.
     *
     * @return ItemInterface Le parent de l'élément courant
     */
	public function end()
	{
		return $this->parent;
	}
	
	/**
	 * Définit directement le contenu HTML d'un élément de menu.
	 *
	 * @param  string $html Le code HTML
	 * @param  string $type Le type de contenu ('content', 'prefix', 'suffix')
	 * @return ItemInterface L'élément courant
	 */
	public function setHtml($html, $type = 'content')
	{
		$this->html[$type] = $html;
		
		return $this;
	}
	
	/**
	 * Retourne le contenu HTML d'un élément du menu.
	 *
	 * @param  string $type Le type de contenu ('content', 'prefix', 'suffix')
	 * @return string Le code HTML
	 */
	public function getHtml($type = 'content')
	{
		return isset($this->html[$type]) ? $this->html[$type] : '';
	}
	
	/**
	 * Définit la valeur du compteur de tâches. S'il vaut null, il ne sera pas 
	 * affiché du tout. Pour toute valeur numérique (y compris zéro), il sera 
	 * affiché avec la valeur concernée.
	 *
	 * @param  null|integer $count La valeur du compteur (null pour désactiver)
	 * @return ItemInterface L'élément courant
	 */
	public function setCount($count)
	{
	    $this->count = $count;
	    
	    return $this;
	}
	
	/**
	 * Retourne la valeur du compteur de tâches.
	 *
	 * @return null|integer La valeur du compteur (null si désactivé)
	 */
	public function getCount()
	{
	    return $this->count;
	}
	
	/**
	 * Définit si l'élément doit être immédiatement précédé d'une séparation.
	 *
	 * @param  boolean $separator
	 * @return ItemInterface L'élément courant
	 */
	public function setSeparator($separator = true)
	{
	    $this->separator = (boolean) $separator;
	    
	    return $this;
	}
	
	/**
	 * Est-ce que l'élément doit être précédé d'une séparation ?
	 *
	 * @return boolean
	 */
	public function isSeparator()
	{
	    return $this->separator;
	}
	
	/**
	 * Protège l'accès à l'élément aux personnes munies de certains droits.
	 *
	 * @param  array|string $credentials Un ou plusieurs droits à vérifier
	 * @return ItemInterface L'élément courant
	 */
	public function secure($credentials)
	{
	    $this->credentials = $credentials;
	    $this->setDisplay($this->isDisplayed() && $this->isAccessAllowed());
	    
	    return $this;
	}
	
	/**
	 * Vérifie si l'accès à l'élément est autorisé.
	 *
	 * @return boolean
	 */
	public function isAccessAllowed()
	{
	    $credentials = $this->credentials;
	    $method = is_array($credentials) ? array_shift($credentials) : 'or';
	    
	    return $this->checkAccess($credentials, $method);
    }
	
	/**
	 * Définit le poids de l'élément. Les éléments les plus lourds sont 
	 * affichés en bas, les plus légers en haut.
	 *
	 * @param  integer $weight
	 * @return ItemInterface L'élément courant
	 */
	public function setWeight($weight)
	{
		$this->weight = $weight;
		
		return $this;
	}
	
	/**
	 * Retourne le poids de l'élément.
	 *
	 * @return integer
	 */
	public function getWeight()
	{
		return $this->weight;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function reorderChildren($order)
    {
        //Commence par prendre en compte le poids des éléments si aucun 
        //ordre n'a été spécifié.
		if (empty($order))
		{
			$weights = array();
			$order = array();
			foreach ($this->children as $child)
			{
				if (!isset($weights[$child->getWeight()]))
				{
					$weights[$child->getWeight()] = array();
				}
				$weights[$child->getWeight()][] = $child->getName();
				
				if ($child->hasChildren())
				{
					$child->reorderChildren(null);
				}
			}
			
			ksort($weights);
			$order = call_user_func_array('array_merge', $weights);
		}
		
		return parent::reorderChildren($order);
	}
	
	/**
	 * Vérifie les droits d'accès à partir d'une liste de droits.
	 *
	 * @param array|string $rights Un ou plusieurs droits à vérifier
	 * @param string $method La méthode de combinaison
	 * @return boolean
	 */
	private function checkAccess($rights, $method = 'or')
	{
		if (is_bool($rights))
		{
		   return $rights;
	    }
		if (is_string($rights))
		{
		    $rights = array($rights);
	    }
	    
		$final = array();
		$method = (mb_strtolower($method) == 'or');
		foreach ($rights as $item)
		{
			if (is_array($item))
			{
				$method2 = array_shift($item);
				$final[] = $this->checkAccess($item, $method2);
			}
			else
			{
			    if (is_bool($item))
			    {
				    $final[] = $item;
			    }
			    elseif (!is_string($item))
			    {
                    $final[] = false;
			    }
			    else
			    {
				    $final[] = verifier($item);
			    }
			}
		}
		
		return in_array($method, $final) === $method;
	}
}