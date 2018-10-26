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

namespace Zco\Bundle\CoreBundle\Paginator\View;

use Zco\Bundle\CoreBundle\Paginator\Paginator;

/**
 * Vue du paginateur représentant une page particulière. Le rendu de la vue
 * affiche une liste de liens permettant de changer de page tandis qu'une
 * itération parcourt les différents objets associés à la vue.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class PaginatorView implements \Countable, \Iterator
{
	private $objects;
	private $count;
	private $number;
	private $paginator;
	private $uri;
	private $index = 0;

	/**
	 * Constructeur de classe (n'a pas à être appelé normalement, appelé par
	 * la méthode getPage() de la classe Paginator).
	 *
	 * @param mixed $objects La liste d'objets associés à la page
	 * @param integer $count Le nombre d'objets contenus dans la page
	 * @param integer $number La page courante
	 * @param Paginator $paginator Le paginateur
	 * @param string $uri L'uri des pages
	 */
	public function __construct($objects, $count, $number, Paginator $paginator, $uri = null)
	{
		$this->objects = $objects;
		$this->count = $count;
		$this->number = $number;
		$this->paginator = $paginator;
		$this->uri = $uri;
	}

	/**
	 * Retourne l'URI utilisée pour générer le rendu de la vue.
	 *
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Définit l'URI utilisée pour générer le rendu de la vue.
	 *
	 * @param string $uri
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;
	}

	/**
	 * Renvoie le numéro de la page.
	 *
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * Renvoie la liste des objets présents sur la page.
	 *
	 * @return mixed
	 */
	public function getObjects()
	{
		return $this->objects;
	}

	/**
	 * Retourne le nombre d'objets à afficher dans la vue.
	 * Implémentation de \Countable.
	 *
	 * @return integer
	 */
	public function count()
	{
		return $this->count;
	}

	/**
	 * Retourne le nombre d'objets total géré par le paginateur.
	 *
	 * @return integer
	 */
	public function countAll()
	{
		return $this->paginator->count();
	}

	/**
	 * Effectue le rendu des liens permettant de naviguer de page en page.
	 *
	 * @param  array $options
	 * @return string
	 */
	public function render(array $options = array())
	{
		if (!isset($this->uri))
		{
			throw new \InvalidArgumentException('You must call setUri() before rendering a paginator view.');
		}

		$bootstrap = isset($options['bootstrap']) && $options['bootstrap'];
		$nbPagesAround = 3;
		$str = array();

		if ($bootstrap)
		{
			$str[] = '<div class="pagination"><ul>';
		}

		//Si on a besoin d'afficher un lien vers la page précédente.
		if ($this->number > 1 && !$bootstrap)
		{
			$str[] = '<a href="'.sprintf($this->uri, $this->number - 1).'">Précédente</a> ';
		}
		elseif ($bootstrap)
		{
			if ($this->number > 1)
			{
				$str[] = '<li><a href="'.sprintf($this->uri, $this->number - 1).'">&larr;</a></li>';
			}
			else
			{
				$str[] = '<li class="disabled"><a href="#">&larr;</a></li>';
			}
		}

		for ($i = 1 ; $i <= $this->paginator->getNbPages() ; $i++)
		{
			if ($bootstrap)
			{
				$str[] = '<li>';
			}
			if (($i < $nbPagesAround) || ($i > $this->paginator->getNbPages() - $nbPagesAround) || (($i < $this->number + $nbPagesAround) && ($i > $this->number - $nbPagesAround)))
			{
				if ($i == $this->number)
				{
					if (!$bootstrap)
					{
						$str[] = '<span class="UI_pageon">'.$i.'</span>&nbsp;';
					}
					else
					{
						$str[] = '<a href="#" class="active">'.$i.'</a>';
					}
				}
				else
				{
					$str[] = '<a href="'.sprintf($this->uri, $i).'">'.$i.'</a>&nbsp;';
				}
			}
			else
			{
				if ($i >= $nbPagesAround && $i <= $this->number - $nbPagesAround)
				{
					$i = $this->number - $nbPagesAround;
				}
				elseif ($i >= $this->number + $nbPagesAround && $i <= $this->paginator->getNbPages() - $nbPagesAround)
				{
					$i = $this->paginator->getNbPages() - $nbPagesAround;
				}

				$splittedUrl = explode('%s', $this->uri);
				$str[] = '<a href="#" onclick="page=prompt(\'Sur quelle page voulez-vous vous rendre ('
					.$this->paginator->getNbPages().' pages) ?\'); if(page) document.location=\''
					.$splittedUrl[0].'\' + page + \''
					.(isset($splittedUrl[1]) ? $splittedUrl[1] : null).'\'; return false;">…</a>&nbsp';
			}
			if ($bootstrap)
			{
				$str[] = '</li>';
			}
		}

		//Si on a besoin d'afficher un lien vers la page suivante.
		if ($this->number < $this->paginator->getNbPages() && !$bootstrap)
		{
			$str[] = '<a href="'.sprintf($this->uri, $this->number + 1).'">Suivante</a>';
		}
		elseif ($bootstrap)
		{
			if ($this->number < $this->paginator->getNbPages())
			{
				$str[] = '<li><a href="'.sprintf($this->uri, $this->number + 1).'">&rarr;</a></li>';
			}
			else
			{
				$str[] = '<li class="disabled"><a href="#">&rarr;</a></li>';
			}
		}

		if ($bootstrap)
		{
			$str[] = '</ul></div>';
		}

		return implode($str);
	}

	/**
	 * Implémentation de \Iterator.
	 */
	public function rewind()
	{
		$this->index = 0;
	}
	public function current()
	{
		return $this->objects[$this->index];
	}
	public function key()
	{
		return $this->index;
	}
	public function next()
	{
		$this->index++;
	}
	public function valid()
	{
		return isset($this->objects[$this->index]);
	}
}
