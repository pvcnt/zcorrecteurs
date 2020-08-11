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

namespace Zco\Bundle\ParserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Événement permettant de manipuler un contenu sous forme d'arbre DOM.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FilterDomEvent extends ParserEvent
{
	private $dom;
	
	/**
	 * Constructeur.
	 *
	 * @param \DomDocument $content L'arbre DOM à filtrer
	 * @param array $options Une liste d'options
	 */
	public function __construct(\DomDocument $dom, array $options = array())
	{
		parent::__construct($options);
		$this->dom = $dom;
	}
	
	/**
	 * Retourne le contenu courant.
 	 *
	 * @return \DomDocument
	 */
	public function getDom()
	{
		return $this->dom;
	}
	
	/**
	 * Remplace l'arbre DOM.
	 *
	 * @param \DomDocument $dom
	 */
	public function setDom(\DomDocument $dom)
	{
		$this->dom = $dom;
	}
}