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

namespace Zco\Bundle\ParserBundle\Parser;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zco\Bundle\ParserBundle\Event\FilterContentEvent;
use Zco\Bundle\ParserBundle\Event\FilterDomEvent;
use Zco\Bundle\ParserBundle\ParserEvents;

/**
 * Parseur générique pour tout langage basé sur un balisage XML. Le parsage 
 * effectif est délégué à des « features » via des événements.
 *
 * @author    mwsaz <mwsaz@zcorrecteurs.fr>
 * @copyright mwsaz <mwksaz@gmail.com> 2010-2012
 */
class zCodeParser implements ParserInterface
{
    private $dispatcher;
	
	/**
	 * Constructeur.
	 *
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct(EventDispatcherInterface $dispatcher)
	{
	    $this->dispatcher = $dispatcher;
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse($text, array $options = array())
	{
		$text = trim($text);
		if ($text === '')
		{
			return '';
		}
		
		//Transformation du texte sous sa forme de chaîne de caractères.
		$text = str_replace("\r\n", "\n", $text);
		list($continue, $text) = $this->callContentListeners(ParserEvents::PRE_PROCESS_TEXT, $text, $options);
		if (!$continue)
		{
		    return $text;
		}
		
		//Chargement du texte dans une structure DOM et gestion des erreurs.
		libxml_use_internal_errors(true);
  		libxml_clear_errors();
		$dom = $this->textToDom($text);
		if($e = libxml_get_errors())
		{
			return $this->generateErrorReport($e[0], $text);
		}
		
		//Transformation du texte sous sa forme d'arbre DOM.
		list($continue, $dom) = $this->callDomListeners(ParserEvents::PROCESS_DOM, $dom, $options);
		if (!$continue)
		{
		    return $this->domToText($dom);
		}
		
		//Rapatriement du texte vers une chaîne de caractères affichable.
		$text = $this->domToText($dom);
		
		//Transformation du texte sous sa forme de chaîne de caractères à nouveau.
		list(, $text) = $this->callContentListeners(ParserEvents::POST_PROCESS_TEXT, $text, $options);
	    
		return $text;
	}
	
	/**
	 * Transforme le texte en un arbre DOM.
	 *
	 * @param  string $texte Texte à parser
	 * @return \DomDocument
	 */
	private function textToDom($text)
	{
	    list(, $text) = $this->callContentListeners(ParserEvents::PREPARE_XML, $text);
		$xml = '<zcode>'."\n".$text."\n".'</zcode>';
		
  		$dom = new \DomDocument();
  		$dom->loadXML($xml);
  		
  		return $dom;
	}
	
	/**
	 * Transforme un arbre DOM en texte.
	 *
	 * @param  \DomDocument $dom L'arbre DOM
	 * @return string
	 */
	private function domToText(\DomDocument $dom)
	{
	    $nodes = $dom->getElementsByTagName('zcode');
		$text = trim($dom->saveXML($nodes->item(0)));
		$text = trim(substr($text, strlen('<zcode>'), -strlen('</zcode>')));
		
		return $text;
	}
	
	/**
	 * Appelle le gestionnaire d'événements pour filtrer une chaîne de caractères.
	 * 
	 * @param string $name Le nom de l'événement
	 * @param string $content La chaîne à filtrer
	 * @param array $options Liste d'options
	 * @return array Liste avec un booléen indiquant si on poursuit le parsage 
	 *               et la nouvelle chaîne
	 */
	private function callContentListeners($name, $content, array $options = array())
	{
		$event = new FilterContentEvent($content, $options);
		$this->dispatcher->dispatch($name, $event);
		
		if ($event->isProcessingStopped())
		{
			return array(false, $event->getContent());
		}
	    
	    return array(true, $event->getContent());
	}
	
	/**
	 * Appelle le gestionnaire d'événements pour filtrer un arbre DOM.
	 * 
	 * @param string $name Le nom de l'événement
	 * @param string $content L'abre DOM à filtrer
	 * @param array $options Liste d'options
	 * @return array Liste avec un booléen indiquant si on poursuit le parsage 
	 *               et le nouvel arbre
	 */
	private function callDomListeners($name, \DomDocument $dom, array $options = array())
	{
		$event = new FilterDomEvent($dom, $options);
		$this->dispatcher->dispatch($name, $event);
		
		if ($event->isProcessingStopped())
		{
			return array(false, $event->getDom());
		}
	    
	    return array(true, $event->getDom());
	}
	
	/**
	 * Génère un rapport d'erreur suite à une malformation dans le XML.
	 * 
	 * @param  \LibXMLError $e L'erreur survenue
	 * @param  string $xml Le code XML fautif
	 * @return string Rapport d'erreur en HTML
	 */
	private function generateErrorReport(\LibXMLError $e, $xml)
	{
		$lignes = explode("\n", $xml);
		$out = 'Une ou plusieurs erreurs ont été trouvées dans votre zCode.<br/>'
		      .'Erreur n<sup>o</sup>&nbsp;'.$e->code.' : ';
		$balises = $message = null;
		if ($e->code == 38 || $e->code == 65 || $e->code == 39)
		{
			$message = 'L\'attribut est malformé.';
		}
		elseif ($e->code == 76)
		{
			$balises = sscanf($e->message,
				'Opening and ending tag mismatch: %s line '
				.'%i and %s');
			$message = 'Les balises <em>'.$balises[0].'</em> et <em>'.$balises[2].'</em>'
			          .' s\'entremêlent à la ligne '.($e->line - 1).'.';
		}
		elseif ($e->code == 502)
		{
			$balises = sscanf($e->message,
				'Syntax of value for attribute %s of %s is not valid');
			if ($balises[0] === null)
			{
				$balises = sscanf($e->message,
					'Value %s for attribute %s of %s is not among the enumerated set');
				$balises = array($balises[1], $balises[2]);
			}
			$message = 'La valeur de l\'attribut <em>'.$balises[0].'</em> de la balise '
			          .'<em>'.$balises[1].'</em> est invalide (ligne '.($e->line - 1).').';
		}
		elseif ($e->code == 504)
		{
			preg_match(
				'`^Element (.+) content does not follow the DTD, expecting '
				.'\\(((?:[a-zA-Z9-9_-]+.?( . )?)+)\\).?, got`', $e->message, $balises);
			$message = 'La balise <em>'.$balises[1].'</em> doit uniquement contenir les balises ';
			$balises = explode($balises[3], $balises[2]);
			foreach ($balises as &$bal)
			{
				$bal = '<em>'.str_replace(array('?', '+', '*'), '', $bal).'</em>';
			}
			$message .= implode(', ', $balises).' (ligne '.($e->line - 1).').';
			$e->line = 0; // N'afficherait que la première balise
		}
		elseif ($e->code == 515)
		{
			$balises = sscanf($e->message,
				'Element %s is not declared in %s list of possible children');
			$message = 'La balise <em>'.$balises[0].'</em> ne peut pas être';
			if ($balises[1] == 'zcode')
			{
				$message .= ' à la racine.';
			}
			else
			{
				$message .= ' contenue dans '
			        	 .'la balise <em>'.$balises[1].'</em> (ligne '.($e->line - 1).').';
        		}
		}
		elseif ($e->code == 518)
		{
			$balises = sscanf($e->message,
				'Element %s does not carry attribute %s');
			$message = 'L\'attribut <em>'.$balises[1].'</em> de la balise '
			          .'<em>'.$balises[0].'</em> est manquant (ligne '.($e->line - 1).').';
		}
		elseif ($e->code == 533)
		{
			$balises = sscanf($e->message,
				'No declaration for attribute %s of element %s');
			$message = 'L\'attribut <em>'.$balises[0].'</em> n\'existe pas '
			          .'pour la balise <em>'.$balises[1].'</em> (ligne '.($e->line - 1).').';
		}
		else
		{
			$message = $e->message;
		}
		
		$out .= $message.'<br />';
		$ligne = ($e->line - 1);
		if ($ligne > 0 && isset($lignes[$ligne]))
		{
			$l = str_replace(
				array('&lt;', '&gt;', '&amp;', '&quot;'),
				array('<', '>', '&', '"'),
				$lignes[$ligne]
			);

			//Décalage provoqué par les entités HTML
			$diff = strlen($lignes[$ligne]) - strlen($l);
			$column = $e->column - 1 - $diff;

			$out .= '<code>'.htmlspecialchars($l).'<br/>';
			if ($column > 0)
			{
				$out .= str_repeat('-', $column).'^';
			}
			$out .= '</code>';
		}
		
		return $out.'<hr />'.nl2br(htmlspecialchars($xml));
	}
}
