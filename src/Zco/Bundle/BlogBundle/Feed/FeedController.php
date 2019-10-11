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

namespace Zco\Bundle\BlogBundle\Feed;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Classe permettant de définir des flux de syndication à haut niveau, en
 * définissant juste les méthodes récupérer les éléments du flux, et avoir
 * les propriétés à afficher.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class FeedController extends Controller
{
	protected $object = null;

	/**
	 * Constructeur de classe. Récupère l'objet associé au flux.
	 */
	public function indexAction()
	{
		$response = new Response($this->renderFeed());
		$response->headers->set('Content-Type', 'application/atom+xml');
		return $response;
	}

	/**
	 * Méthode de récupération des éléments du flux. Doit être réécrite par
	 * chaque flux.
	 * @access protected
	 */
	protected function getItems()
	{
	}

	/**
	 * S'occupe d'afficher le flux et de gérer sa mise en cache.
	 * @access public
	 */
	public function renderFeed()
	{
		$cache_file = 'xml/'.strtolower(get_class($this)).'.xml';
		$lifetime = isset($this->lifetime) ? $this->lifetime : 3600;
		$cache = $this->get('cache');

		if(($content = $cache->fetch($cache_file)) === false)
		{
			$objects = $this->getItems();
			$params = array(
				'title' => $this->getAttr('title'),
				'description' => $this->getAttr('description'),
				'link' => $this->getAttr('link'),
				'updated' => $this->getAttr('updated')
			);
			$this->hasAttr('authors') && $params['authors'] = $this->getAttr('authors');
			$feed = new Syndication($params);

			foreach($objects as $object)
			{
				$params = array(
					'title' => $this->getItemAttr('title', $object),
					'link' => $this->getItemAttr('link', $object)
				);
				$this->hasItemAttr('description') && $params['description']
					= $this->parse($this->getItemAttr('description', $object));
				$this->hasItemAttr('authors') && $params['authors'] = $this->getItemAttr('authors', $object);
				$this->hasItemAttr('updated') && $params['updated'] = $this->getItemAttr('updated', $object);
				$this->hasItemAttr('comments') && $params['comments'] = $this->getItemAttr('comments', $object);
				$this->hasItemAttr('source') && $params['source'] = $this->getItemAttr('source', $object);

				$this->hasItemAttr('enclosureUrl') && $params['enclosureUrl'] = $this->getItemAttr('enclosureUrl', $object);
				$this->hasItemAttr('content') && $params['content'] = $this->parse($this->getItemAttr('content', $object));

				$feed->addItem($params);
			}

			//Sauvegarde du XML
			$content = $feed->writeString();
			$cache->save($cache_file, $content, $lifetime);
		}

		return $content;
	}

	/**
	 * Récupère la valeur d'une propriété propre au flux.
	 * @access protected
	 * @param string $attr		Le nom de l'attribut.
	 * @return string
	 */
	protected function getAttr($attr)
	{
		if(isset($this->$attr))
			return $this->$attr;
		elseif(method_exists($this, 'get'.ucfirst($attr)))
			return $this->{'get'.ucfirst($attr)}();
		else
			throw new \RuntimeException(sprintf('No feed attribute %s found in feed %s.', $attr, get_class($this)));
	}

	/**
	 * Vérifie si propriété propre au flux a été définie.
	 * @access protected
	 * @param string $attr		Le nom de l'attribut.
	 * @return boolean
	 */
	protected function hasAttr($attr)
	{
		return isset($this->$attr) || method_exists($this, 'get'.ucfirst($attr));
	}

	/**
	 * Récupère la valeur d'une propriété propre à un élément.
	 * @access protected
	 * @param string $attr		Le nom de l'attribut.
	 * @return string
	 */
	protected function getItemAttr($attr, $object)
	{
		if(isset($this->{'item'.ucfirst($attr)}))
			return $this->{'item'.ucfirst($attr)};
		elseif(method_exists($this, 'getItem'.ucfirst($attr)))
			return $this->{'getItem'.ucfirst($attr)}($object);
		else
			throw new \RuntimeException(sprintf('No item attribute %s found in feed %s.', $attr, get_class($this)));
	}

	/**
	 * Vérifie si une propriété propre à un élément a été définie.
	 * @access protected
	 * @param string $attr		Le nom de l'attribut.
	 * @return boolean
	 */
	protected function hasItemAttr($attr)
	{
		return isset($this->{'item'.ucfirst($attr)}) || method_exists($this, 'getItem'.ucfirst($attr));
	}

	/**
	 * Parse du texte pour que l'affichage se passe correctement depuis un
	 * agrégateur (url relatives =>	absolues).
	 * @access protected
	 * @param string $text
	 * @return string
	 */
	protected function parse($text)
	{
		$text = preg_replace('`src="/?(images|uploads)`', 'src="'.$this->getAttr('link').'/$1', $text);
		$text = preg_replace('`(href|src)="/`', '$1="'.$this->getAttr('link').'/', $text);
		return $text;
	}

	/**
	 * Par défaut, le lien du flux est la valeur de la constante URL_SITE. Peut
	 * bien entendu être réécrite.
	 * @access protected
	 * @return string
	 */
	protected function getLink()
	{
		return defined('URL_SITE') ? URL_SITE : '';
	}

	protected function getItemUpdated($item)
	{
		return $this->getUpdated($item);
	}

	protected function getUpdated($object)
	{
		if(!isset($object, $object['pubtime']) && !isset($this->latest, $this->latest['pubtime']))
			return null;
		if(isset($object, $object['pubtime']))
			return date('c', $object['pubtime']);
		
		return date('c', $this->latest['pubtime']);
	}
}
