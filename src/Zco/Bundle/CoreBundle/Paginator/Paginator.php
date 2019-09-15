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

namespace Zco\Bundle\CoreBundle\Paginator;

use Zco\Bundle\CoreBundle\Paginator\View\PaginatorView;

/**
 * Paginateur simple gérant les tableaux et les requêtes Doctrine1.
 * Librement inspiré du composant éponyme de Django.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Paginator implements \Countable
{
    private $adapter;
    private $objects;
	private $orphans;
	private $maxPerPage;
	private $count;
	private $nbPages;
	private $hasOrphans;

	/**
	 * Constructeur.
	 *
	 * @param mixed $objects Les objets à paginer
	 * @param integer $perPage Le nombre d'éléments souhaités par page
	 * @param integer $orphans Nombre minimum d'éléments pour ouvrir une nouvelle page
	 */
	public function __construct($objects, $maxPerPage, $orphans = 0)
	{
		$this->objects    = $objects;
		$this->maxPerPage = $maxPerPage;
		$this->orphans    = $orphans;
	}

	/**
	 * Retourne une vue du paginateur sur une page spécifique. Cette vue 
	 * permettra d'afficher l'ensemble des liens du paginateur et d'accéder 
	 * aux objets de la page active
	 *
	 * @param  integer $number La page demandée
	 * @param  string $uri L'URL à utiliser pour générer les liens
	 * @return PaginatorView
	 */
	public function createView($number, $uri = null)
	{
	    $this->initialize();
	    
		if (!is_int($number))
		{
			throw new \InvalidArgumentException(sprintf(
			    'Page number must be an integer (got %s).', gettype($number)
			));
		}
		if ($number <= 0 || $number > $this->nbPages)
		{
			throw new \InvalidArgumentException(sprintf(
			    'Page number must be between 1 and %s (got %s).', 
			    $this->nbPages, $number
			));
		}

        $offset = ($number - 1) * $this->maxPerPage;        
		$limit = ($number != $this->nbPages || !$this->hasOrphans) ? $this->maxPerPage : null;
		$slice = $this->adapter->slice($this->objects, $offset, $limit);
		
		return new PaginatorView($slice, $this->adapter->count($slice), $number, $this, $uri);
	}

	/**
	 * Renvoie le nombre total d'objets à paginer.
	 * Implémentation de \Countable.
	 *
	 * @return integer
	 */
	public function count()
	{
	    $this->initialize();
	    
		return $this->count;
	}

	/**
	 * Renvoie le nombre de pages à afficher.
	 *
	 * @return integer
	 */
	public function getNbPages()
	{
	    $this->initialize();
	    
		return $this->nbPages;
	}
	
	/**
	 * Initialise la paginateur en calculant tous les attributs nécessaires.
	 * Délègue les calculs liés à la collection d'objets à un adaptateur.
	 */
	private function initialize()
	{
	    if (isset($this->adapter))
	    {
	        return;
        }
        
	    $this->adapter = $this->getAdapter($this->objects);
        $this->count = $this->adapter->count($this->objects);
        $this->nbPages = ceil($this->count / $this->maxPerPage);
        
        //Gestion des orphelins de la dernière page.
		if ($this->count - $this->maxPerPage * ($this->nbPages - 1) <= $this->orphans)
		{
			$this->nbPages--;
			$this->hasOrphans = true;
		}
		else
		{
		    $this->hasOrphans = false;
		}

		//On affiche toujours au moins une page, même vide.
		if ($this->nbPages == 0)
		{
			$this->nbPages = 1;
		}
	}
	
	/**
	 * Renvoie un adaptateur adapté au type d'objet. La liste des adaptateurs 
	 * disponibles est gérée de façon statique car on n'a pas besoin de plus.
	 *
	 * @param  mixed $objects Les objets à paginer
	 * @return AdapterInterface
	 */
	private function getAdapter($objects)
	{
	    $adapters = array(
	        new Adapter\ArrayAdapter(),
	        new Adapter\Doctrine1Adapter(),
	    );
	    
	    foreach ($adapters as $adapter)
	    {
	        if ($adapter->supports($objects))
	        {
	            return $adapter;
	        }
	    }
	    
	    throw new \InvalidArgumentException(sprintf(
	        'No paginator adapter found for type %s.', gettype($objects)
	    ));
	}
}
