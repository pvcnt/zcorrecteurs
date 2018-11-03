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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * Ensemble de fonctions aidant à afficher les données de façon lisible 
 * et habituelle en suivant toutes les conventions françaises.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class HumanizeHelper extends Helper
{
	/**
	 * "Humanise" des nombres sous une forme plus habituelle en toutes lettres 
	 * pour des petites valeurs (de 1 à 9).
	 *
	 * @param  string $number Nombre à transformer.
	 * @param  integer $cap MAJUSCULE ou MINUSCULE ?
	 * @return string
	 */
	public function apnumber($number, $cap = MINUSCULE)
	{
		$conversion = array(
			1 => 'un',
			2 => 'deux',
			3 => 'trois',
			4 => 'quatre',
			5 => 'cinq',
			6 => 'six',
			7 => 'sept',
			8 => 'huit',
			9 => 'neuf',
		);

		if (isset($conversion[$number]))
		{
			return $cap == MAJUSCULE ? ucfirst($conversion[$number]) : $conversion[$number];
		}
		else
		{
			return (string) $number;
		}
	}

    /**
     * Formate une date. Remplace les dates proches par une structure plus 
     * naturelle (hier, après-demain, il y a 3 min ,etc.).
     *
     * @author DJ Fox <djfox@zcorrecteurs.fr>
     * @param  string $dateheure La date à formater, dans un format compréhensible par strtotime
     * @param  integer $majuscule Commencer par une majuscule ou une minuscule ?
     * @param  integer $datetime Afficher la date et l'heure ou juste la date ?
     * @return string Une date formatée
     */
	public function dateformat($dateheure, $majuscule = MAJUSCULE, $datetime = DATETIME)
	{
		return dateformat($dateheure, $majuscule, $datetime);
	}

    /**
     * Choisit le suffixe à afficher de façon à gérer les formes plurielles.
     *
     *   'chev' . pluriel(3, 'aux', 'al') // affichera 'chevaux'
     *
     * @author Zopieux
     * @param  string $nb Le nombre à tester
     * @param  string $alt Le suffixe de la forme plurielle
     * @param  string $normal Le suffixe de la forme singulière
     * @return string Le suffixe à afficher
     */
	public function pluriel($nb, $alt = 's', $normal = '')
	{
		return pluriel($nb, $alt, $normal);
	}
    
    /**
     * Calcule une différence entre deux dates.
     *
     * @author mwsaz <mwsaz@zcorrecteurs.fr>
     * @param  integer $t1 Le premier timestamp
     * @param  integer $t2 Le second timestamp
     * @return string
     */
	public function datediff($t1, $t2 = 0)
	{
		$diff = abs($t2 - $t1);
		$m = (int)($diff / 60);
		$h = (int)($m / 60);
		$j = (int)($h / 24);

		$s = $diff % 60;
		$m = $m % 60;
		$h = $h % 24;

		$out = array();
		if ($j) $out[] = $j.' jour'.($j > 1 ? 's' : '');
		if ($h) $out[] = ($h < 10 ? '0' : '').$h.' h';
		if ($m) $out[] = ($m < 10 ? '0' : '').$m.' min';
		if ($s) $out[] = ($s < 10 ? '0' : '').$s.' s';

		return implode(' ', $out);
	}

	/**
	 * Choisit le format d'affichage d'un nombre en fonction de sa nullité.
	 *
	 * @author Zopieux
	 * @param  integer $nb		Le nombre à tester
	 * @param  string $alt		La forme à afficher si le nombre est nul
	 * @return array
	 */
	public function aucun($nb, $alt = 'Aucun')
	{
		return ($nb < 1 || empty($nb)) ? $alt : $nb;
	}

    /**
     * Formate un nombre pour l'afficher suivant les normes françaises. Permet
     * également de choisir la précision de l'affichage et éventuellement de 
     * diminuer cette précision.
     *
     *   numberformat(12223) // affichera '12 223'
     *   numberformat(12223, -2) // affichera '12 200'
     *
     * @param  integer $nb Le nombre à formater
     * @param  integer $precision La précision de l'affichage
     * @return string
     */
	public function numberformat($nb, $precision = 2)
	{
		return number_format(round($nb, $precision), ($precision < 0 ? 0 : $precision), ',', ' ');
	}

    /**
     * Formate une taille en octets de façon plus naturelle.
     *
     * @param  integer|float $size La taille à formater
     * @return string
     */
	public function sizeformat($size)
	{
		return sizeformat($size);
	}

	/**
	 * Formate une adresse email pour la protéger contre les robots. Cette 
	 * protection reste relativement basique, mais devrait être un bon compromis
	 * entre paranoïa et efficacité.
	 *
	 * @param  string $email
	 * @return string
	 */
	public function email($email)
	{
		$retval = '';
		for ($i = 0, $len = strlen($email); $i < $len; ++$i)
		{
			$retval .= '&#'.ord($email[$i]);
		}

		return $retval;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'humanize';
	}
}
