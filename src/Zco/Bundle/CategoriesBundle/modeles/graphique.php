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
 * Génération d'une image représentative de la strucuture des catégories.
 * _Très_ utile pour le débogage.
 *
 * @author mwsaz
 */

/**
 * Affiche le graphique des catégories.
 * @param $racine		int	ID de la catégorie à prendre comme racine.
 * @param $afficherBornes	bool	Afficher ou non les bornes des catégories.
 * @param $inverser		bool	Sens du graphique : false = racine à gauche, true = racine en haut.
 */
function AfficherGraphique($racine = 1, $afficherBornes = false, $inverser = false)
{
	$config = array(
		'police'	=> 2,
		'marge'		=> 5,
	);

	$imagestring = $inverser ? 'imagestringup' : 'imagestring';

	$categories = ListerEnfants($racine, true);
	$racine = current($categories);

	$niveauMax = $borneMax = $tailleMax = -1;
	$niveaux = array();

	foreach($categories as &$categorie)
	{
		// Changement des infos par rapport à la racine (0 relatif).
		$categorie['cat_niveau'] -= $racine['cat_niveau'];
		$categorie['cat_gauche'] -= $racine['cat_gauche'];
		$categorie['cat_droite'] -= $racine['cat_gauche'];

		// Récupération des maximums du niveau, de la borne droite et de la longueur du nom
		if($categorie['cat_niveau'] > $niveauMax)
			$niveauMax = $categorie['cat_niveau'];
		if($categorie['cat_droite'] > $borneMax)
			$borneMax = $categorie['cat_droite'];
		$categorie['cat_nom'] = utf8_decode($categorie['cat_nom']);
		if(($taille = strlen($categorie['cat_nom'])) > $tailleMax)
			$tailleMax = $taille;

		// Arrangement des catégories par niveau
		$niveaux[$categorie['cat_niveau']][] = &$categorie;
	}
	$config['largeurPolice'] = imagefontwidth($config['police']);
	$config['largeurCat'] = $tailleMax * $config['largeurPolice'];
	$config['tailleBorneMax'] = 0;
	if($afficherBornes)
	{
		$config['tailleBorneMax'] = $config['largeurPolice'] * strlen($borneMax + $racine['cat_gauche']);
		$config['largeurCat'] += ($config['tailleBorneMax'] + $config['marge']) * 2;
	}
	$config['largeurCat'] += $config['marge'] * 2;
	$config['hauteurPolice'] = imagefontheight($config['police']);
	$config['hauteurCat'] = $config['hauteurPolice'] + (int)($config['hauteurPolice'] / 3);

	// Si on veut afficher l'image verticalement
	if($inverser)
	{
		$h = $config['hauteurCat'];
		$config['hauteurCat'] = $config['largeurCat'];
		$config['largeurCat'] = $h;

		// Calcul des dimensions de l'image, les derniers '+ 1' sont pour la bordure.
		$dimensions = array(
			$config['largeurCat'] * ($borneMax) + 1,
			$config['hauteurCat'] * ($niveauMax + 1) + 1,
		);
	}
	else
	{
		$dimensions = array(
			$config['largeurCat'] * ($niveauMax + 1) + 1,
			$config['hauteurCat'] * ($borneMax) + 1
		);
	}

	// Création de l'image, des couleurs
	$image = imagecreate($dimensions[0], $dimensions[1]);
	$couleurs = array(
		'fond'		=> imagecolorallocate($image, 0, 255, 0),
		'rectangle'	=> imagecolorallocate($image, 239, 239, 239),
		'bordure'	=> imagecolorallocate($image, 214, 214, 214),
		'texte'		=> imagecolorallocate($image, 0, 0, 0),
		'bornes'	=> imagecolorallocate($image, 150, 150, 150)
	);

	// Dessin des catégories
	foreach($niveaux as &$niveau)
		foreach($niveau as &$categorie)
		{
			$coordonnees = CoordonneesCategorie($config, $categorie, $inverser);
			list($x1, $y1, $x2, $y2, $tx, $ty) = $coordonnees;

			// Bordure et fond
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $couleurs['rectangle']);
			imagerectangle($image, $x1, $y1, $x2, $y2, $couleurs['bordure']);

			// Nom de la catégorie
			$imagestring($image, $config['police'], $tx, $ty,
				$categorie['cat_nom'], $couleurs['texte']);

			if($afficherBornes)
			{
				list($x1, $y1, $x2, $y2)
					= CoordonneesBornes($config, $categorie, $inverser, $coordonnees);

				$imagestring($image, $config['police'], $x1, $y1,
					$categorie['cat_gauche'] + $racine['cat_gauche'],
					$couleurs['bornes']);
				$imagestring($image, $config['police'], $x2, $y2,
					$categorie['cat_droite'] + $racine['cat_gauche'],
					$couleurs['bornes']);
			}
		}
	imagecolortransparent($image, $couleurs['fond']);
	header('Content-type: image/png');
	imagepng($image);
}

function CoordonneesCategorie(&$config, &$categorie, &$inverser)
{
	if(!$inverser)
	{
		// Coin supérieur gauche
		$x1 = $config['largeurCat'] * $categorie['cat_niveau'];
		$y1 = $config['hauteurCat'] * $categorie['cat_gauche'];
		// Coin inférieur droit
		$x2 = $config['largeurCat'] * ($categorie['cat_niveau'] + 1);
		$y2 = $config['hauteurCat'] * $categorie['cat_droite'];

		$tx = 1 + $x1 + $config['marge'];
		if($config['tailleBorneMax'])
			$tx += $config['tailleBorneMax'] + $config['marge'];
		$ty = $config['hauteurCat'] * ($categorie['cat_gauche'] + $categorie['cat_droite']) / 2
			- (int)($config['hauteurPolice'] / 2);
	}
	else
	{
		// Coin supérieur gauche
		$x1 = $config['largeurCat'] * $categorie['cat_gauche'];
		$y1 = $config['hauteurCat'] * ($categorie['cat_niveau'] + 1);
		// Coin inférieur droit
		$x2 = $config['largeurCat'] * $categorie['cat_droite'];
		$y2 = $config['hauteurCat'] * $categorie['cat_niveau'];

		$tx = $config['largeurCat'] * ($categorie['cat_gauche'] + $categorie['cat_droite']) / 2
			- (int)($config['hauteurPolice'] / 2);
		$ty = $y1 - $config['marge'];
		if($config['tailleBorneMax'])
			$ty -= $config['tailleBorneMax'] + $config['marge'];
	}
	return array($x1, $y1, $x2, $y2, $tx, $ty);
}

function CoordonneesBornes(&$config, &$categorie, &$inverser, &$coordonnees)
{
	$x1 = $config['marge'];
	$x2 = $x1 + $config['tailleBorneMax'];

	if(!$inverser)
	{
		$x1 = $coordonnees[0] + $x1; // x1
		$x2 = $coordonnees[2] - $x2; // x2
		$y1 = $y2 = $coordonnees[5]; // ty
	}
	else
	{
		$y1 = $coordonnees[1] - $x1; // y1
		$y2 = $coordonnees[3] + $x2; // y2
		$x1 = $x2 = $coordonnees[4]; // tx
	}
	return array($x1, $y1, $x2, $y2);
}

