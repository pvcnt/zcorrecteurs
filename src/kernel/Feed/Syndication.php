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

/**
 * Classe représentant un flux de syndication en cours de construction. Cette
 * classe est une API de plus bas niveau à laquelle fait appel Feed.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 *         mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Feed_Syndication
{
	protected $xml;
	protected $params;

	/**
	 * Constructeur. Reçoit les paramètres concernant le flux en lui-même.
	 * Paramètres requis : title, description, updated et link.
	 * @access public
	 * @param array $params		Liste des paramètres.
	 */
	public function __construct($params)
	{
		if (!isset($params['title'], $params['description'], $params['link'], $params['updated']))
			throw new InvalidArgumentException(sprintf(
				'%s constructor must receive at least title, description, '
				.'updated and link arguments (got %s)',
				get_class($this), implode(', ', array_keys($params))
			));
		$this->params = $params;

		$this->xml = new DomDocument();
		$this->xml->formatOutput = true;

		$this->root = $this->appendElement($this->xml, 'feed', null, array(
			'xmlns' => 'http://www.w3.org/2005/Atom'
		));

		$this->title = $this->appendElement($this->root, 'title', $this->params['title']);
		$this->description = $this->appendElement($this->root, 'subtitle', $this->params['description']);
		$this->link = $this->appendElement($this->root, 'link', null, array('href' => $this->params['link']));
		$this->appendElement($this->root, 'id', rtrim(URL_SITE, '/').'/');
		$this->appendElement($this->root, 'generator', 'Zingle');

		$this->atom_link = $this->appendElement($this->root, 'link', null, array(
			'href' => $this->params['link'].$_SERVER['REQUEST_URI'],
			'rel' => 'self',
			'type' => 'application/atom+xml'));

		$this->appendElement($this->root, 'updated', $params['updated']);
		if(!empty($params['authors']))
			foreach($params['authors'] as &$a)
			{
				$author = $this->appendElement($this->root, 'author');
				$this->appendElement($author, 'name', $a[0]);
				if(!empty($a[1]))
					$this->appendElement($author, 'email', $a[1]);
			}
	}

	/**
	 * Ajoute un élément au flux.
	 * Paramètres requis : title, updated et link.
	 * Paramètres facultatifs : authors, content, enclosureUrl.
	 * @access public
	 * @param array $params		Liste des paramètres.
	 */
	public function addItem($params)
	{
		if(!isset($params['title'], $params['updated'], $params['link']))
			throw new InvalidArgumentException(sprintf(
				'%s addItem method must receive at least title, description, '
				.'updated and link arguments  (got %s)',
				get_class($this), implode(', ', array_keys($params))
			));
		$item = $this->appendElement($this->root, 'entry');

		if(!empty($params['enclosureUrl']))
		{
			$this->appendElement($item, 'link', null, array(
				'rel' => 'enclosure',
				'href' => $params['enclosureUrl']
			));
		}

		$this->appendElement($item, 'title', $params['title']);
		if(empty($params['content']))
			$this->appendElement($item, 'content', $params['link']);
		else
			$this->appendElement($item, 'content', $params['content'], array('type' => 'html'));

		if(!empty($params['guid']))
			$this->appendElement($item, 'id', $params['guid']);
		else
			$this->appendElement($item, 'id', $params['link']);


		if(!empty($params['description']))
			$this->appendElement($item, 'summary', $params['description'], array('type' => 'html'));

		$this->appendElement($item, 'link', null, array('href' => $params['link']));
		$this->appendElement($item, 'published', $params['updated']);
		$this->appendElement($item, 'updated', $params['updated']);

		if(!empty($params['authors']))
			foreach($params['authors'] as &$a)
			{
				$author = $this->appendElement($item, 'author');
				$this->appendElement($author, 'name', $a[0]);
				if(!empty($a[1]))
					$this->appendElement($author, 'email', $a[1]);
			}
	}

	/**
	 * Ajoute un élément à l'arbre XML.
	 * @access protected
	 * @param DomElement $item		Le nœud parent.
	 * @param string $key			Le nom de la balise.
	 * @param string|null $value	Son contenu (null pour aucun).
	 * @param array $params			Liste d'attributs du nœud.
	 * @return DomElement			L'objet qui a été créé.
	 */
	protected function appendElement(&$item, $key, $value = null, $params = array(), $cdata = true)
	{
		$element = $this->xml->createElement($key);
		$element = $item->appendChild($element);
		if(!empty($params))
		{
			foreach($params as $attr => $val)
			{
				$element->setAttribute($attr, $val);
			}
		}
		if(!is_null($value))
		{
			$element_value = $this->xml->createCDATASection($value);
			$element_value = $element->appendChild($element_value);
		}

		return $element;
	}

	/**
	 * Écrit le flux dans un fichier avec l'encodage spécifié.
	 * @access public
	 * @param string $file			Le chemin vers le fichier.
	 * @param string $encoding		L'encodage du fichier.
	 */
	public function write($file, $encoding = 'UTF-8')
	{
		$this->xml->encoding = $encoding;
		$this->xml->save($file);
	}

	/**
	 * Retourne la chaine XML résultant du flux avec l'encodage spécifié.
	 * @access public
	 * @param string $encoding		L'encodage.
	 * @return string
	 */
	public function writeString($encoding = 'UTF-8')
	{
		$this->xml->encoding = $encoding;
		return $this->xml->saveXML();
	}

	/**
	 * Affiche le flux XML avec l'encodage spécifié. Envoie l'en-tête approprié.
	 * @access public
	 * @param string $encoding		L'encodage.
	 */
	public function output($encoding = 'UTF-8')
	{
		header("Content-type: text/xml; charset=".strtolower($encoding));
		$this->xml->encoding = $encoding;
		echo $this->xml->saveXML();
	}

	public function setLatest($l)
	{
		$this->appendElement($this->root, 'updated', date('c', $l['pubtime']));
	}
}
