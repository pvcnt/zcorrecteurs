<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Zco\Bundle\ParserBundle\Parser\ParserFeature;

/**
 * Parseur générique pour tout langage basé sur un balisage XML. Le parsage 
 * effectif est délégué à des « features » via des événements.
 *
 * @author    mwsaz <mwsaz@zcorrecteurs.fr>
 * @copyright mwsaz <mwksaz@gmail.com> 2010-2012
 */
class zCodeParser implements ParserInterface
{
    /** @var ParserFeature[] */
    private $features;
	
	/**
	 * Constructeur.
	 *
	 * @param iterable $features
	 */
	public function __construct(iterable $features)
	{
	    $this->features = $features;
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
		
		// Transformation du texte sous sa forme de chaîne de caractères.
		$text = str_replace("\r\n", "\n", $text);
		foreach ($this->features as $feature) {
		    $text = $feature->preProcessText($text, $options);
        }
		
		// Chargement du texte dans une structure DOM et gestion des erreurs.
		libxml_use_internal_errors(true);
  		libxml_clear_errors();
		$dom = $this->textToDom($text, $options);
		if($e = libxml_get_errors())
		{
			return $this->generateErrorReport($e[0], $text);
		}
		
		// Transformation du texte sous sa forme d'arbre DOM.
        foreach ($this->features as $feature) {
            $dom = $feature->processDom($dom, $options);
        }

		// Rapatriement du texte vers une chaîne de caractères affichable.
		$text = $this->domToText($dom);
		
		// Transformation du texte sous sa forme de chaîne de caractères à nouveau.
        foreach ($this->features as $feature) {
            $text = $feature->postProcessText($text, $options);
        }
	    
		return $text;
	}

	private function textToDom($text, array $options)
	{
        foreach ($this->features as $feature) {
            $text = $feature->prepareXml($text, $options);
        }

		$xml = '<zcode>'."\n".$text."\n".'</zcode>';
		
  		$dom = new \DomDocument();
  		$dom->loadXML($xml);
  		
  		return $dom;
	}

	private function domToText(\DomDocument $dom)
	{
	    $nodes = $dom->getElementsByTagName('zcode');
		$text = trim($dom->saveXML($nodes->item(0)));
		$text = trim(substr($text, strlen('<zcode>'), -strlen('</zcode>')));
		
		return $text;
	}

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
