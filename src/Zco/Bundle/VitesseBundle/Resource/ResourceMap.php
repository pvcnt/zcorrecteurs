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

namespace Zco\Bundle\VitesseBundle\Resource;

/**
 * Structure de données mémorisant les relations entre les différentes 
 * ressources.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ResourceMap
{
	private $resourceGraph;
	private $aliases;
	
	/**
	 * Constructeur.
	 *
	 * @param string $cacheDir Dossier où sont stockées les données en cache
	 */
	public function __construct($cacheDir)
	{
	    $this->resourceGraph = require($cacheDir.'/resourceGraph.php');
	    $this->aliases = require($cacheDir.'/aliases.php');
	}
	
	/**
	 * Résout les dépendances pour une série de ressources.
	 *
	 * @param  array $symbols Ressources demandées à l'inclusion
	 * @return array Toutes les ressources à inclure
	 */
    public function resolveResources(array $symbols)
    {
        $resolved = array();
        foreach ($symbols as $symbol)
        {
            if (!empty($resolved[$symbol]))
            {
                continue;
            }
            $resolved = $this->resolveResource($symbol, $resolved);
        }
        
        return $resolved;
    }
    
    /**
     * Retourne le nom d'une ressource pour Assetic à partir de son 
     * nom logique.
     *
     * @param string  $name Nom logique d'une ressource
     * @return string Identifiant de la ressource pour Assetic
     */
    public function getAssetName($name)
	{
	    if (!isset($this->aliases[$name]))
	    {
	        throw new \InvalidArgumentException(sprintf(
	            'Resource "%s" does not exist.', $name
	        ));
	    }
	    
	    return $this->aliases[$name];
	}
    
    /**
     * Résout les dépendances d'une unique ressource.
     *
     * @param  string $symbol Ressource demandée à l'inclusion
     * @param  array $resolved Liste de la totalité des ressources 
     *                        à inclure en cours de construction
     * @return array Toutes les ressources à inclure
     */
    private function resolveResource($symbol, array $resolved = array())
    {
        if (!array_key_exists($symbol, $this->resourceGraph))
        { 
            throw new \InvalidArgumentException(sprintf(
                'Resource "%s" was not found in the resource graph.', 
                $symbol
            ));
        }
        
        foreach ($this->resourceGraph[$symbol] as $requires)
        {
            if (!empty($resolved[$requires]))
            {
                continue;
            }
            
            $resolved = $this->resolveResource($requires, $resolved);
        }

        $resolved[$symbol] = true;

        return $resolved;
    }
}