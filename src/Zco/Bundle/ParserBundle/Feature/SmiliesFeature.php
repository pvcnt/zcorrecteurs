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

namespace Zco\Bundle\ParserBundle\Feature;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\ParserBundle\ParserEvents;
use Zco\Bundle\ParserBundle\Event\FilterContentEvent;

/**
 * Composant de remplacement des smilies.
 *
 * @author	mwsaz <mwsaz@zcorrecteurs.fr>
 * @copyright mwsaz <mwksaz@gmail.com> 2010-2012
 */
class SmiliesFeature implements EventSubscriberInterface
{
	 /**
	  * Liste des smilies disponibles avec en clé le code du smilie et en 
	  * valeur le nom de l'image associée.
	  *
	  * @var array
	  */
	private static $smilies = array(
		':)' => 'smile.png',
		':D' => 'heureux.png',
		';)' => 'clin.png',
		':p' => 'langue.png',
		':lol:' => 'rire.gif',
		':euh:' => 'unsure.gif',
		':(' => 'triste.png',
		':o' => 'huh.png',
		':colere2:' => 'mechant.png',
		'o_O' => 'blink.gif',
		'^^' => 'hihi.png',
		':-°' => 'siffle.png',
		':ange:' => 'ange.png',
		':colere:' => 'angry.gif',
		':diable:' => 'diable.png',
		':magicien:' => 'magicien.png',
		':ninja:' => 'ninja.png',
		'>_<' => 'pinch.png',
		':pirate:' => 'pirate.png',
		':\'(' => 'pleure.png',
		':honte:' => 'rouge.png',
		':soleil:' => 'soleil.png',
		':waw:' => 'waw.png',
		':zorro:' => 'zorro.png'
	);
	
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	 {
		  return array(
				ParserEvents::POST_PROCESS_TEXT => 'postProcessText',
		  );
	 }
	 
	 /**
	  * Remplace les smilies dans le texte.
	  *
	  * @param FilterContentEvent $event
	  */
	 public function postProcessText(FilterContentEvent $event)
	 {
		static $recherche = array();
		static $smilies	= array();
		
		  if (!$recherche || !$smilies)
		  {
				foreach (self::$smilies as $smilie => $url)
				{
					 $smilie = htmlspecialchars($smilie);
					 $smilies[$smilie] = '<img src="/bundles/zcocore/img/zcode/smilies/'
						  .$url.'" alt="'.$smilie.'"/>';
					 $recherche[] = preg_quote($smilie, '`');
				}
				$recherche = implode('|', $recherche);
				$recherche = '`(\s|^|>)('.$recherche.')(\s|$|<)(?![^><]*"[^>]*>)`';
		  }
		  
		  //On essaye d'éviter les smilies qui sont dans les attributs.
		  $event->setContent(preg_replace_callback($recherche, function($m) use($smilies) {
				return $m[1].$smilies[$m[2]].$m[3];
		  }, $event->getContent()));
	 }
}
