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

namespace Zco\Bundle\CoreBundle\Menu\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\ItemInterface;

/**
 * Événement permettant de modifier le contenu d'un menu.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FilterMenuEvent extends Event
{
    private $request;
	private $root;
	private $template;
	
	/**
	 * Constructeur.
	 *
	 * @param Request $request La requête concernée
	 * @param ItemInterface $root Le menu à filtrer
	 * @param string $template Le nom du layout dans lequel le menu est inclus
	 */
	public function __construct(Request $request, ItemInterface $root, $template = null)
	{
	    $this->request = $request;
		$this->root = $root;
		$this->template = $template;
	}
	
	/**
	 * Retourne la requête concernée.
	 *
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * Retourne le nom du menu.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->root->getName();
	}
	
	/**
	 * Retourne le menu à filtrer.
	 *
	 * @return ItemInterface
	 */
	public function getRoot()
	{
		return $this->root;
	}
	
	/**
	 * Retourne le nom du layout dans lequel le menu est inclus.
	 *
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}
}