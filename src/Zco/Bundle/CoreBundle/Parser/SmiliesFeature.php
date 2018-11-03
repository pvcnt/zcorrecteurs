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

namespace Zco\Bundle\CoreBundle\Parser;

/**
 * Composant de remplacement des smilies.
 *
 * @author	mwsaz <mwsaz@zcorrecteurs.fr>
 * @copyright mwsaz <mwksaz@gmail.com> 2010-2012
 */
class SmiliesFeature extends AbstractFeature
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
	  * Remplace les smilies dans le texte.
	  *
	  * @param string $content
      * @param array $options
      * @return string
	  */
	 public function postProcessText(string $content, array $options): string
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
		  return preg_replace_callback($recherche, function($m) use($smilies) {
				return $m[1].$smilies[$m[2]].$m[3];
		  }, $content);
	 }
}
