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

namespace Zco\Bundle\RecrutementBundle;

/**
 * Calcule la différence entre deux textes avec wdiff.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>, vincent1870 <vincent@zcorrecteurs.fr>
 */

class Diff
{
	private $original;
	private $modifie;
	private $fautes = array();
	private $wdiff;
	private $html;

	public function __construct(\RecrutementTest $test, $texte)
	{
		$this->original = $texte;
		$this->modifie = $test['texte'];

		$this->computeDiff();
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

	protected function wdiff(array $boundaries = array())
	{
		$t = microtime(true);
		$original =  '/tmp/diff-'.$t.'-o';
		$modifie = '/tmp/diff-'.$t.'-m';

		file_put_contents($original, $this->original);
		file_put_contents($modifie, $this->modifie);

		$o = '';
		foreach($boundaries as $opt => $value)
			$o .= '--'.$opt.' "'.$value.'" ';
		$o = substr($o, 0, -1);

		$out = shell_exec('/opt/local/bin/wdiff '.$o.' '.$original.' '.$modifie);

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

	protected function computeDiff()
	{
		if ($this->wdiff)
		{
			return;
		}
		
		$opts = array(
			'start-delete' => '<-',
			'end-delete'   => '->',
			'start-insert' => '<+',
			'end-insert'   => '+>'
		);
		$this->wdiff = $this->wdiff($opts);
		
		$corrige = preg_replace_callback('`\\<([+-])([^>]+)\\1\\>`',
			array($this, 'faute'), $this->wdiff);
		$corrige = preg_replace(
			'`\\<-([^>]+)-\\>(\s+\\<\\+[^>]\\+\\>)? \\<\\<`',
			'$1$2', $corrige);
		$corrige = preg_replace('`\\{([^|}]+)(?:|[^}])*\\}`', '$1', $corrige);
		$corrige = str_replace(array('+> <+', '-> <-'), ' ', $corrige);

		$this->wdiff = $corrige;
	}

	protected function faute($match)
	{
		static $supprime = null;

		if ($match[1] === '-')
		{
			$supprime = $match[2];
			return $match[0];
		}
		
		//Si on est sur un marqueur de faute à corriger.
		if ($match[1] === '+' &&
		        ($pos1 = strpos($match[2], '{')) !== false &&
		        ($pos2 = strpos($match[2], '}')) !== false &&
		        $pos1 < $pos2)
		{
			$avant = trim(substr($match[2], 0, $pos1));
			$dedans = trim(substr($match[2], $pos1 + 1, $pos2 - $pos1 - 1));
			$apres = trim(substr($match[2], $pos2 + 1));

			if ($avant !== '')
			{
				$avant = preg_replace('` {2,}`', ' ', trim($avant));
				$avant = '<+'.$avant.'+> ';
				$supprime = null;
			}
			if ($apres !== '')
			{
				$apres = preg_replace('` {2,}`', ' ', trim($apres));
				$apres = ' <+'.$apres.'+>';
			}

			list($correction, $faute, $type) = explode('|', $dedans);
			$mots = explode(',', $correction);
			
			if (!isset($this->fautes[$type]))
			{
				$this->fautes[$type] = array();
			}

			//Le candidat a bien détecté un problème au niveau de ce mot et 
			//l'a corrigé, reste à savoir s'il l'a bien corrigé.
			if ($supprime !== null)
			{
				//Si jamais le candidat a correctement corrigé le mot.
				if (in_array($supprime, $mots))
				{
					//$this->supprime--;
					$this->fautes[$type][] = array(true, $faute, $supprime);
					$supprime = '';
					$apres = '';

					return $avant.'<<'.$apres;
				}
				
				//Sinon le candidat n'a pas bien corrigé le mot.
				$this->fautes[$type][] = array(false, $faute, $supprime);
				$supprime = '';
			}
			
			//La première possibilité est la graphie recommandée.
			return $avant.'<+'.$mots[0].'+>'.$apres;
		}
		
		//Sinon le candidat a simplement ajouté du texte.
		if ($match[1] === '+')
		{
			return $match[0];
		}
		
		return '';
	}
}
