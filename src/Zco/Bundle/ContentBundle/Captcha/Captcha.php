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

namespace Zco\Bundle\ContentBundle\Captcha;

/**
 * Génération de captchas ; fortement inspiré de Cryptograph.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Captcha
{
	protected
		$config,
		$polices,
		$caracteres,
		$decalage,
		$image;

	const DEFAULT_CONFIG = [
        'fond' => [
            'rouge' => 255,
            'vert' => 255,
            'bleu' => 255,
            'transparent' => true,
        ],
        'caracteres' => [
            'rouge' => 0,
            'vert' => 0,
            'bleu' => 0,
            'aleatoire' => true, // Couleur choisie aléatoirement
            'luminosite' => 1, // Luminosité des caractères de 1 (sombre) à 4 (clair)
            'transparence' => 10, // Transparence des caractères de 0 (opaque) à 127 (invisible)
            'liste' => 'ABCDEFGHKLMNPRTWXYZ',
            'nombre' => 4, // Nombre de caractères
            'espacement' => 20, // Espacement des caractères
            'taille' => ['min' => 14, 'max' => 16],
            'polices' => ['luggerbu.ttf'],
            'anglemax' => 25,
        ],
        'brouillage' => [
            'flouGaussien' => false, // Appliquer un flou gaussien
            'niveauxGris' => false, // Image en noir et blanc (niveaux de gris)
            'bruit' => [
                'pixels' => ['min' => 10, 'max' => 50],
                'lignes' => ['min' => 1, 'max' => 5],
                'cercles' => ['min' => 1, 'max' => 5],
                'type' => 'dessous', // Le bruit est-il par-dessus ou par-dessous les caractères ?
                'epaisseur' => ['min' => 1, 'max' => 4],
            ],
        ],
        'format' => 'png',
        'intervalle' => 2, // Temps d'attente entre deux générations d'image par le même client (s)
        'largeur' => 130,
        'hauteur' => 40,
        'cadre' => true,
    ];

	public function __construct($config = self::DEFAULT_CONFIG)
	{
		$this->config = $config;

		$this->antiflood();
		$this->chargerPolices();
	}

	public function afficher()
	{
		$this->generer();
		imagepng($this->image);
	}

	static public function verifier($texte)
	{
		if(!isset($_SESSION['captcha_texte']))
			return false;
		$ok = strtolower($texte) == strtolower($_SESSION['captcha_texte']);
		unset($_SESSION['captcha_texte']);
		return $ok;
	}

	protected function generer()
	{
		$this->texte = $this->genererTexte();
		$this->genererCaracteres();
		$this->calculerDecalage();

		$this->creerImage();
		if($this->config['brouillage']['bruit']['type'] == 'dessus')
		{
			$this->dessinerTexte();
			$this->dessinerBruit();
		}
		else
		{
			$this->dessinerBruit();
			$this->dessinerTexte();
		}
		$this->dessinerCadre();
		$this->appliquerFiltres();
	}

	protected function genererTexte()
	{
		$texte = '';
		$max = strlen($this->config['caracteres']['liste']) - 1;
		for($i = 0; $i < $this->config['caracteres']['nombre']; $i++)
			$texte .= $this->config['caracteres']['liste'][mt_rand(0, $max)];
		return $_SESSION['captcha_texte'] = $texte;
	}

	protected function antiflood()
	{
		$temps = time();
		if(!isset($_SESSION['captcha_prochainEssai'])
			|| $_SESSION['captcha_prochainEssai'] <= $temps)
		{
			$_SESSION['captcha_prochainEssai']
				= $temps + $this->config['intervalle'];
			return true;
		}
		sleep($_SESSION['captcha_prochainEssai'] - $temps);
		return false;
	}

	protected function chargerPolices()
	{
		$this->polices = array();
		foreach($this->config['caracteres']['polices'] as &$police)
			$this->polices[] = BASEPATH.'/data/fonts/'.$police;
	}

	protected function choisirPolice()
	{
		return $this->polices[array_rand($this->polices)];
	}

	protected function choisirCouleur()
	{
		return imagecolorallocate($this->image,
			mt_rand(0, 255),
			mt_rand(0, 255),
			mt_rand(0, 255));
	}

	protected function choisirEpaisseur()
	{
		return imagesetthickness($this->image, mt_rand(
			$this->config['brouillage']['bruit']['epaisseur']['min'],
			$this->config['brouillage']['bruit']['epaisseur']['max']));
	}

	protected function choisirCouleurTexte()
	{
		$ok = false;
		$essais = 0;
		do
		{
			$rouge = mt_rand(0, 255);
			$vert = mt_rand(0, 255);
			$bleu = mt_rand(0, 255);

			$somme = $rouge + $vert + $bleu;

			switch($this->config['caracteres']['luminosite'])
			{
				case 1: if ($somme < 200) $ok = true; break;
             			case 2: if ($somme < 400) $ok = true; break;
             			case 3: if ($somme > 500) $ok = true; break;
             			case 4: if ($somme > 650) $ok = true; break;
           			default : $ok = true;
			}
			$essais++;
		}
		while(!$ok && $essais < 10);

		return imagecolorallocatealpha($this->image, $rouge, $vert, $bleu,
			$this->config['caracteres']['transparence']);
	}

	protected function genererCaracteres()
	{
		$x = 0;
		$this->caracteres = array();
		for($i = 0; $i < $this->config['caracteres']['nombre']; $i++)
		{
			$caractere = array(
				'police'	=> $this->choisirPolice(),
				'angle'		=> mt_rand(
					-$this->config['caracteres']['anglemax'],
					$this->config['caracteres']['anglemax']
				),
				'taille'	=> mt_rand(
					$this->config['caracteres']['taille']['min'],
					$this->config['caracteres']['taille']['max']
				),
				'caractere'	=> $this->texte[$i],
				'x'		=> $x,
				'y'		=> $this->config['hauteur'] / 2
					+ mt_rand(0, $this->config['hauteur'] / 5)
			);
			$x += $this->config['caracteres']['espacement'];
			$this->caracteres[] = $caractere;
		}
	}

	protected function calculerDecalage()
	{
		// Création d'une version en noir et blanc du texte

		$image = imagecreate($this->config['largeur'], $this->config['hauteur']);
		$blanc = imagecolorallocate($image, 255, 255, 255);
		$noir = imagecolorallocate($image, 0, 0, 0);

		foreach($this->caracteres as &$caractere)
			imagettftext($image, $caractere['taille'], $caractere['angle'], $caractere['x'],
				$caractere['y'], $noir, $caractere['police'], $caractere['caractere']);

		// Calcul du racadrage horizontal du cryptogramme
		$debut = 0;
		for($x = 0; $x < $this->config['largeur'] && !$debut; $x++)
			for($y = 0; $y < $this->config['hauteur'] && !$debut; $y++)
			{
				// On a trouvé un bout de lettre
				if(imagecolorat($image, $x, $y) == $noir)
					$debut = $x;
			}

		$fin = 0;
		for($x = $this->config['largeur'] - 1; $x >= 0 && !$fin; $x--)
			for($y = 0; $y < $this->config['hauteur'] && !$fin; $y++)
			{
				// On a trouvé un bout de lettre
				if(imagecolorat($image, $x, $y) == $noir)
					$fin = $x;
			}

		$this->decalage = round(($this->config['largeur'] - $fin + $debut) / 2);
		imagedestroy($image);
	}

	protected function creerImage()
	{
		$this->image = imagecreate($this->config['largeur'], $this->config['hauteur']);
		$fond = imagecolorallocate($this->image,
			$this->config['fond']['rouge'],
			$this->config['fond']['vert'],
			$this->config['fond']['bleu']);
		if($this->config['fond']['transparent'])
			imagecolortransparent($this->image, $fond);
	}

	protected function dessinerTexte()
	{
		foreach($this->caracteres as &$caractere)
		{
			$couleur = $this->choisirCouleurTexte();
			imagettftext($this->image, $caractere['taille'], $caractere['angle'],
				$caractere['x'] + $this->decalage,
				$caractere['y'], $couleur,
				$caractere['police'], $caractere['caractere']);
		}
	}

	protected function dessinerBruit()
	{
		$pixels = mt_rand($this->config['brouillage']['bruit']['pixels']['min'],
			$this->config['brouillage']['bruit']['pixels']['max']);
		$lignes = mt_rand($this->config['brouillage']['bruit']['lignes']['min'],
			$this->config['brouillage']['bruit']['lignes']['max']);
		$cercles = mt_rand($this->config['brouillage']['bruit']['cercles']['min'],
			$this->config['brouillage']['bruit']['cercles']['max']);

		for($i = 0; $i < $pixels; $i++)
		{
			$couleur = $this->choisirCouleur();
			imagesetpixel($this->image,
				mt_rand(0, $this->config['largeur'] - 1),
				mt_rand(0, $this->config['hauteur'] - 1),
				$couleur);
		}
		for($i = 0; $i < $lignes; $i++)
		{
			$couleur = $this->choisirCouleur();
			$this->choisirEpaisseur();
			imageline($this->image,
				mt_rand(0, $this->config['largeur'] - 1),
				mt_rand(0, $this->config['hauteur'] - 1),
				mt_rand(0, $this->config['largeur'] - 1),
				mt_rand(0, $this->config['hauteur'] - 1),
				$couleur);
		}
		for($i = 0; $i < $cercles; $i++)
		{
			$couleur = $this->choisirCouleur();
			$this->choisirEpaisseur();
			imagearc($this->image,
			 	mt_rand(0, $this->config['largeur'] - 1),
			 	mt_rand(0, $this->config['hauteur'] - 1),
			 	$rayon = mt_rand(5, $this->config['largeur'] / 3),
			 	$rayon, mt_rand(20, 360), mt_rand(20, 360), $couleur);
		}
		imagesetthickness($this->image, 1);
	}

	protected function dessinerCadre()
	{
		if($this->config['cadre'])
		{
			$couleur = imagecolorallocate($this->image,
				($this->config['fond']['rouge'] * 3 + $this->config['caracteres']['rouge']) / 4,
				($this->config['fond']['vert'] * 3 + $this->config['caracteres']['vert']) / 4,
				($this->config['fond']['bleu'] * 3 + $this->config['caracteres']['bleu']) / 4);
			imagerectangle($this->image, 0, 0,
				$this->config['largeur'] - 1,
				$this->config['hauteur'] - 1,
				$couleur);
		}
	}

	protected function appliquerFiltres()
	{
		if(!function_exists('imagefilter'))
			return;
		if($this->config['brouillage']['niveauxGris'])
			imagefilter($this->image, IMG_FILTER_GRAYSCALE);
		if($this->config['brouillage']['flouGaussien'])
			imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
	}
}
