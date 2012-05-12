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

/*
 * Modèle s'occupant du rapport d'activité tous zCos confondus.
 *
 * @author DJ Fox, Ziame
 * @begin 25/07/2008
 * @last 25/07/2008 vincent1870
 */

/**
 * On récupère les informations sur l'évolution globale de la correction des tutos.
 * @return array.
 */
function RecupDonneesTutos()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Calcul du nombre de corrections de news
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 1 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0 AND soumission_news = 1
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_corrections_news_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//Calcul du nombre de REcorrections de news
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 1 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0 AND soumission_news = 1
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_recorrections_news_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//Calcul du nombre de corrections de mini-tutos
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 1 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0 AND soumission_news = 0
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_corrections_mini_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//Calcul du nombre de REcorrections de mini-tutos
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 1 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0 AND soumission_news = 0
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_recorrections_mini_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//Calcul du nombre de corrections de big-tutos
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 2 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_corrections_big_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//Calcul du nombre de REcorrections de big-tutos
	$stmt = $dbh->prepare("
	SELECT MONTH(correction_date_fin) AS mois, COUNT(correction_id) AS nombre_total_corrections
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
	WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
	AND soumission_type_tuto = 2 AND YEAR(NOW()) = YEAR(correction_date_fin)
	AND correction_abandonee = 0
	GROUP BY MONTH(correction_date_fin) ASC");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$nombre_total_recorrections_big_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//On règle pour que tout soit sur la même échelle des mois
	for ($compteur = 1 ; $compteur <= date('n', time()) ; $compteur++)
	{
		//Les news (correction puis recorrection)
		if (!isset($nombre_total_corrections_news_nt))
		{$nombre_total_corrections_news[$compteur - 1] = 0;}
		else
		{
			foreach ($nombre_total_corrections_news_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_corrections_news[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_corrections_news[$compteur - 1] = 0;}
			}
		}
		if (!isset($nombre_total_recorrections_news_nt))
		{$nombre_total_recorrections_news[$compteur - 1] = 0;}
		else
		{
			foreach ($nombre_total_recorrections_news_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_recorrections_news[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_recorrections_news[$compteur - 1] = 0;}
			}
		}

		//Les mini-tutos (correction puis recorrection)
		if (!isset($nombre_total_corrections_mini_nt))
		{$nombre_total_corrections_mini[$compteur - 1] = 0;}
		else
		{
			foreach ($nombre_total_corrections_mini_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_corrections_mini[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_corrections_mini[$compteur - 1] = 0;}
			}
		}
		if (!isset($nombre_total_recorrections_mini_nt))
		{$nombre_total_corrections_mini[$compteur - 1] = 0;}
		else
		{
			foreach	($nombre_total_recorrections_mini_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_recorrections_mini[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_recorrections_mini[$compteur - 1] = 0;}
			}
		}
		//Les big-tutos (correction puis recorrection)
		if (!isset($nombre_total_corrections_big_nt))
		{$nombre_total_corrections_big[$compteur - 1] = 0;}
		else
		{
			foreach	($nombre_total_corrections_big_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_corrections_big[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_corrections_big[$compteur - 1] = 0;}
			}
		}
		if (!isset($nombre_total_recorrections_big_nt))
		{$nombre_total_recorrections_big[$compteur - 1] = 0;}
		else
		{
			foreach	($nombre_total_recorrections_big_nt AS $element)
			{
				if ($element['mois'] == $compteur)
				{$nombre_total_recorrections_big[$compteur - 1] = $element['nombre_total_corrections'];
				break;}
				else
				{$nombre_total_recorrections_big[$compteur - 1] = 0;}
			}
		}
	}

	//Retour des résultats dans un tableau associatif
	return array(
	'nombre_total_corrections_news' => $nombre_total_corrections_news,
	'nombre_total_recorrections_news' => $nombre_total_recorrections_news,
	'nombre_total_corrections_mini' => $nombre_total_corrections_mini,
	'nombre_total_recorrections_mini' => $nombre_total_recorrections_mini,
	'nombre_total_corrections_big' => $nombre_total_corrections_big,
	'nombre_total_recorrections_big' => $nombre_total_recorrections_big,
	);
}

/**
 * Récupération des statistiques de correction par zCorrecteur.
 * @return array
 */
function RecupDonneesTutosParZcorr()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT u1.utilisateur_id, u1.utilisateur_pseudo, g1.groupe_id, MONTH(c1.correction_date_fin) AS mois, COUNT(MONTH(c1.correction_date_fin)) AS nombre_tutos
	FROM zcov2_utilisateurs u1
	LEFT JOIN zcov2_push_corrections c1 ON c1.correction_id_correcteur = u1.utilisateur_id
	LEFT JOIN zcov2_groupes g1 ON g1.groupe_id = u1.utilisateur_id_groupe
	WHERE u1.utilisateur_id_groupe = 3 OR u1.utilisateur_id_groupe = 4 OR u1.utilisateur_id_groupe = 5
	AND c1.correction_date_debut IS NOT NULL AND c1.correction_date_fin IS NOT NULL
	GROUP BY utilisateur_id, MONTH(correction_date_fin)
	ORDER BY g1.groupe_id DESC, u1.utilisateur_id ASC, MONTH(c1.correction_date_fin) ASC;");
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$retour_nt[] = $resultat;
	}
	$stmt->closeCursor();

	//On range les données pour qu'elles soient directement exploitables par le grapheur
	$retour = array();
	foreach ($retour_nt AS $element)
	{
		if ($element['mois'] - 1 >= 0)
		{$retour[$element['groupe_id']][$element['utilisateur_pseudo']][$element['mois'] - 1] = $element['nombre_tutos'];}
	}
	foreach ($retour AS &$element)
	{
		foreach ($element AS &$sousElement)
		{
			for ($compteur = 1 ; $compteur <= date('n', time()) ; $compteur++)
			{
				if (!isset($sousElement[$compteur - 1]) || empty($sousElement[$compteur - 1]))
				{$sousElement[$compteur - 1] = 0;}
			}
			ksort($sousElement);
		}
	}
	RETURN $retour;
}