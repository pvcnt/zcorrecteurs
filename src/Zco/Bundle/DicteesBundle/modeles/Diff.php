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

/**
 * Calcule la différence entre deux textes avec wdiff.
 *
 * @author mwsaz
 */

class DicteeDiff
{
	protected $original, $modifie;
	protected $ajoute, $supprime;
	protected $wdiff, $html, $fautes;

	// Ne prend pas en compte la typographie pour la notation
	public static function doubleDiff($Dictee, $texte)
	{
		$diff = new DicteeDiff(
			htmlspecialchars(str_replace('’', "'", $texte)),
			htmlspecialchars(str_replace('’', "'", $Dictee->texte)));
		$diff2 = new DicteeDiff(
			htmlspecialchars(StandardiserTexte($texte)),
			htmlspecialchars(StandardiserTexte($Dictee->texte)));
		$diff->fautes = $diff2->fautes;

		return $diff;
	}



	public function __construct($original, $modifie)
	{
		$this->original = $original;
		$this->modifie = $modifie;

		$this->calculerDiff();
		$this->buildHtml();
	}

	public function __toString()
	{
		return $this->html;
	}

	public function fautes()
	{
		return $this->fautes;
	}

	protected function wdiff($boundaries)
	{
		$t = microtime(true);
		$original =  '/dev/shm/diff-'.$t.'-o';
		$modifie = '/dev/shm/diff-'.$t.'-m';

		file_put_contents($original, $this->original);
		file_put_contents($modifie, $this->modifie);

		$o = '';
		foreach($boundaries as $opt => $value)
			$o .= '--'.$opt.' "'.$value.'" ';
		$o = substr($o, 0, -1);

		$out = shell_exec('wdiff '.$o.' '.$original.' '.$modifie);

		unlink($original);
		unlink($modifie);

		return $out;
	}

	protected function buildHtml()
	{
		$conv = array(
			'<-' => '<del>',
			'->' => '</del>',
			'<+' => '<ins>',
			'+>' => '</ins>'
		);
		$this->html = str_replace(array_keys($conv), $conv, $this->wdiff);
	}

	protected function calculerDiff()
	{
		$opts = array(
			'start-delete' => '<-',
			'end-delete'   => '->',
			'start-insert' => '<+',
			'end-insert'   => '+>'
		);
		$this->wdiff = $this->wdiff($opts);
		$this->ajoute = $this->supprime = 0;

		$corrige = preg_replace_callback('`\\<([+-])([^>]+)\\1\\>`',
			array($this, 'faute'), $this->wdiff);
		$corrige = preg_replace(
			'`\\<-([^>]+)-\\>(\s+\\<\\+[^>]\\+\\>)? \\<\\<`',
			'$1$2', $corrige);
		$corrige = preg_replace('`\\{([^|}]+)(?:|[^}])*\\}`', '$1', $corrige);
		$corrige = str_replace(array('+> <+', '-> <-'), ' ', $corrige);

		$this->wdiff = $corrige;
		$this->fautes = round(min($this->ajoute, $this->supprime))
		              + abs($this->ajoute - $this->supprime);
	}

	protected function faute($match)
	{
		static $supprime = null;

		if ($match[1] === '-')
		{
			$supprime = $match[2];
			$this->supprime++;
			return $match[0];
		}
		elseif ($match[1] === '+' &&
		        ($pos1 = strpos($match[2], '{')) !== false &&
		        ($pos2 = strpos($match[2], '}')) !== false &&
		        $pos1 < $pos2)
		{
			// Plusieurs possibilités

			$avant = trim(substr($match[2], 0, $pos1));
			$dedans = trim(substr($match[2], $pos1 + 1, $pos2 - $pos1 - 1));
			$apres = trim(substr($match[2], $pos2 + 1));

			if ($avant !== '')
			{
				$avant = preg_replace('` {2,}`', ' ', trim($avant));
				$this->ajoute += substr_count($avant, ' ') + 1;
				$avant = '<+'.$avant.'+> ';
				$supprime = null;
			}
			if ($apres !== '')
			{
				$apres = preg_replace('` {2,}`', ' ', trim($apres));
				$this->ajoute += substr_count($apres, ' ') + 1;
				$apres = ' <+'.$apres.'+>';
			}

			$mots = explode('|', $dedans);

			if ($supprime !== null)
			{
				$trouve = in_array($supprime, $mots);
				$supprime = '';

				if ($trouve)
				{
					$this->supprime--;
					$apres = '';


					return $avant.'<<'.$apres;
				}
			}
			// La première possibilité est la graphie recommandée.
			$out = $avant.'<+'.$mots[0].'+>'.$apres;
			return $out;
		}

		if ($match[1] === '+')
		{
			$this->ajoute++;
			return $match[0];
		}
		return '';
	}
}

/**
 * Remplace les caractères "spéciaux" par ceux standards, sur le clavier.
 *
 * @param String  $texte Le texte ou faire les rempalcements.
 * @return String Texte nettoyé.
*/
function StandardiserTexte($texte)
{
	$caracteres = array(
		'æ'   => 'ae',
		'œ'   => 'oe',
		'Æ'   => 'AE',
		'Œ'   => 'OE',

		'À'   => 'A',
		'Ç'   => 'C',
		'É'   => 'E',
		'È'   => 'E',
		'Ê'   => 'E',
		'Ï'   => 'I',
		'Î'   => 'I',
		'Ö'   => 'O',
		'Ô'   => 'O',
		'Ü'   => 'U',
		'Û'   => 'U',
		'Ÿ'   => 'Y',
		'Ŷ'   => 'Y',

		' '   => ' ', // Espace insécable
		'…'   => '...',
		'«'   => '"',
		'»'   => '"',
		'−'   => '-',
		'—'   => '-',
		'’'   => "'",
		'‘'   => "'",
	);

	$texte = str_replace(
		array('« ', ' »', '« ', ' »'),
		array('«', '»', '«', '»'), $texte);

	$texte = str_replace(array_keys($caracteres), $caracteres, $texte);

	return $texte;
}
