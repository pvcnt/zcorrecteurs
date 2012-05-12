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
 * Modèle s'occupant des statistiques globales du site.
 *
 * @author DJ Fox, Ziame
 * @begin 14/11/2007
 * @last 26/03/2009 vincent1870
 */

/**
 * Récupère des statistiques de zCorrection.
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @return array
 */
function RecupStatistiques()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (!($statistiqueszCorrection = Container::getService('zco_core.cache')->Get('statistiques_zcorrection')))
	{
		//Calcul du nombre total de big-tutos
		$stmt = $dbh->prepare("
		SELECT COUNT(soumission_id) AS nombre_total_tutos
		FROM zcov2_push_soumissions
		");
		$stmt->execute();
		$nombre_total_tutos = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du nombre total de mini-tutos
		$stmt = $dbh->prepare("
		SELECT COUNT(soumission_id) AS nombre_total_mini_tutos
		FROM zcov2_push_soumissions
		WHERE soumission_type_tuto = :mini
		");
		$stmt->bindValue(':mini', 1);
		$stmt->execute();
		$nombre_total_mini_tutos = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du temps moyen de correction global
		$stmt = $dbh->prepare("
		SELECT TIME_FORMAT(
		SEC_TO_TIME(
		AVG(
		TIME_TO_SEC(
		timediff(correction_date_fin, correction_date_debut)
		)
		)
		), '%Hh%i'
		) AS temps_moyen_correction_global
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
		");
		$stmt->execute();
		$temps_moyen_correction_global = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du temps moyen de correction de mini-tutos
		$stmt = $dbh->prepare("
		SELECT TIME_FORMAT(
		SEC_TO_TIME(
		AVG(
		TIME_TO_SEC(
		timediff(correction_date_fin, correction_date_debut)
		)
		)
		), '%Hh%i'
		) AS temps_moyen_correction_mini_OU_big
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
		WHERE soumission_type_tuto = :type_tuto
		");
		$mini_tuto = MINITUTO;
		$stmt->bindParam(':type_tuto', $mini_tuto);
		$stmt->execute();
		$temps_moyen_correction_mini = $stmt->fetchColumn();

		//Calcul du temps moyen de correction de big-tutos
		$big_tuto = BIGTUTO;
		$stmt->bindParam(':type_tuto', $big_tuto);
		$stmt->execute();
		$temps_moyen_correction_big = $stmt->fetchColumn();
		$stmt->closeCursor();



		//Calcul du temps moyen de REcorrection global
		$stmt = $dbh->prepare("
		SELECT TIME_FORMAT(
		SEC_TO_TIME(
		AVG(
		TIME_TO_SEC(
		timediff(correction_date_fin, correction_date_debut)
		)
		)
		), '%Hh%i'
		) AS temps_moyen_recorrection_global
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
		");
		$stmt->execute();
		$temps_moyen_recorrection_global = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du temps moyen de REcorrection de mini-tutos
		$stmt = $dbh->prepare("
		SELECT TIME_FORMAT(
		SEC_TO_TIME(
		AVG(
		TIME_TO_SEC(
		timediff(correction_date_fin, correction_date_debut)
		)
		)
		), '%Hh%i'
		) AS temps_moyen_correction_mini_OU_big
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
		WHERE soumission_type_tuto = :type_tuto
		");
		$mini_tuto = MINITUTO;
		$stmt->bindParam(':type_tuto', $mini_tuto);
		$stmt->execute();
		$temps_moyen_recorrection_mini = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du temps moyen de REcorrection de big-tutos
		$big_tuto = BIGTUTO;
		$stmt->bindParam(':type_tuto', $big_tuto);
		$stmt->execute();
		$temps_moyen_recorrection_big = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du nombre de corrections
		$stmt = $dbh->prepare("
		SELECT COUNT(correction_id) AS nombre_total_corrections
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_1 = correction_id
		WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
		");
		$stmt->execute();
		$nombre_total_corrections = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du nombre de REcorrections
		$stmt = $dbh->prepare("
		SELECT COUNT(correction_id) AS nombre_total_corrections
		FROM zcov2_push_soumissions
		LEFT JOIN zcov2_push_corrections ON soumission_id_correction_2 = correction_id
		WHERE correction_date_debut IS NOT NULL AND correction_date_fin IS NOT NULL
		");
		$stmt->execute();
		$nombre_total_recorrections = $stmt->fetchColumn();
		$stmt->closeCursor();

		//Calcul du nombre moyen de (corrections+recorrections) les deux comptés ensembles par zCo (un zAdmin est aussi un zCo)
		$stmt = $dbh->prepare("
		SELECT utilisateur_id, COUNT( correction_id ) AS nombre_moyen_global_par_zco
		FROM zcov2_utilisateurs
		LEFT JOIN zcov2_push_corrections ON utilisateur_id = correction_id_correcteur
		WHERE correction_date_debut IS NOT NULL
		AND correction_date_fin IS NOT NULL
		GROUP BY utilisateur_id
		");
		$retour = array();
		if ($stmt->execute())
		{
			while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$retour[] = $resultat;
			}
			$stmt->closeCursor();
		}
		$nombre_entrees = count($retour);
		$nombre_moyen_global_par_zco = 0;
		foreach($retour as $clef => $valeur)
		{
			$nombre_moyen_global_par_zco += $valeur['nombre_moyen_global_par_zco'];
		}
		$nombre_moyen_global_par_zco = $nombre_entrees > 0 ? $nombre_moyen_global_par_zco / $nombre_entrees : 0;

		//Ici on évite une requête en calculant avec une soustraction le nombre de big tutos.
		$nombre_total_big_tutos = $nombre_total_tutos - $nombre_total_mini_tutos;

		//Retour des résultats dans un tableau associatif
		$statistiqueszCorrection = array(
			'nombre_total_tutos' => $nombre_total_tutos,
			'nombre_total_mini_tutos' => $nombre_total_mini_tutos,
			'nombre_total_big_tutos' => $nombre_total_big_tutos,
			'temps_moyen_correction_global' => $temps_moyen_correction_global,
			'temps_moyen_correction_mini' => $temps_moyen_correction_mini,
			'temps_moyen_correction_big' => $temps_moyen_correction_big,
			'temps_moyen_recorrection_global' => $temps_moyen_recorrection_global,
			'temps_moyen_recorrection_mini' => $temps_moyen_recorrection_mini,
			'temps_moyen_recorrection_big' => $temps_moyen_recorrection_big,
			'nombre_total_corrections' => $nombre_total_corrections,
			'nombre_total_recorrections' => $nombre_total_recorrections,
			'nombre_moyen_global_par_zco' => $nombre_moyen_global_par_zco
		);
		Container::getService('zco_core.cache')->Set('statistiques_zcorrection', $statistiqueszCorrection, 0);
	}
	return $statistiqueszCorrection;
}

/**
 * Récupère les statistiques d'inscription.
 * @author Ziame
 * @param string $classementFils				Le type de période.
 * @param string $classementSQL					Son équivalent en SQL.
 * @param integer $annee						L'année sur laquelle on fait les stats.
 * @param integer $mois							Un mois précis sur lequel grouper les stats (facultatif).
 * @param integer $jour							Un jour précis sur lequel grouper les stats (facultatif).
 */
function RecupStatistiquesInscription($classementFils = 'Mois', $classementSql = 'MONTH', $annee, $mois = 50, $jour = 50)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if ($classementFils === "Heure")
	{$condition = 'YEAR(utilisateur_date_inscription) = '.$annee.' AND MONTH(utilisateur_date_inscription) = '.$mois.' AND DAY(utilisateur_date_inscription) = '.$jour;
		if ($classementSql === "WEEKDAY") {$depart = 0;}
		else {$depart = 1;}}
	else if ($classementFils === "Jour")
	{$condition = 'YEAR(utilisateur_date_inscription) = '.$annee.' AND MONTH(utilisateur_date_inscription) = '.$mois;
		if ($classementSql === "WEEKDAY") {$depart = 0;}
		else {$depart = 1;}}
	else
	{$condition = 'YEAR(utilisateur_date_inscription) = '.$annee;
		if ($classementSql === "WEEKDAY") {$depart = 0;}
		else {$depart = 1;}}

	//Calcul du nombre d'inscriptions
	$stmt = $dbh->prepare('
	SELECT
	'.$classementSql.'(utilisateur_date_inscription) - '.$depart.' AS subdivision,
	COUNT(*) AS nombre_inscriptions,
	ROUND(COUNT(*)/(SELECT COUNT(*) FROM zcov2_utilisateurs WHERE '.$condition.')*100, 1) AS pourcentage_pour_division,
	ROUND(COUNT(*)/(SELECT COUNT(*) FROM zcov2_utilisateurs)*100, 1) AS pourcentage_pour_total
	FROM zcov2_utilisateurs WHERE '.$condition.' AND utilisateur_valide=1 GROUP BY '.$classementSql.'(utilisateur_date_inscription)
	');

	$stmt->execute();

	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$retourNonTraite[] = $resultat;
	}

	//Array des mois en anglais
	$convertisseurMois = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	(int)$mois--; (int)$jour--;

	//On comble les trous (si pour un mois, une journée... il n'y a pas d'inscrit, ça serait bien que la valeur soit quand même présente)
	if ($classementSql === "HOUR")
	{
		//On supprime la fin du jour si ça n'est pas encore passé
		if (($jour + 1).' '.($mois + 1).' '.$annee === date('j n Y', time()))
		{$clauseRepetition = date('G', time()) - 1;}
		else
		{$clauseRepetition = 23;}

		for ($compteur = 0 ; $compteur <= $clauseRepetition ; $compteur++)
		{
			$retour[$compteur]['subdivision'] = $compteur;
			if (!empty($retourNonTraite))
			{
				foreach ($retourNonTraite AS $elementNonTraite)
				{
					if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision'])
					{$retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];$retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];$retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];}
				}
				foreach($retour AS $elementTraite)
				{
					if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total']))
					{$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;}
				}
			}
			else
			{
				$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;
			}
		}
	}
	else if ($classementSql === "DAY")
	{
		//On supprime la fin du mois si ça n'est pas encore passé
		if (($mois + 1).' '.$annee === date("n Y", time()))
		{$clauseRepetition = date('d', time()) - 1;}
		else
		{$clauseRepetition = date('t', strtotime($convertisseurMois[$mois].' '.$annee))-1;}

		for ($compteur = 0 ; $compteur <= $clauseRepetition ; $compteur++)
		{
			$retour[$compteur]['subdivision'] = $compteur;
			if (!empty($retourNonTraite))
			{
				foreach ($retourNonTraite AS $elementNonTraite)
				{
					if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision'])
					{$retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];$retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];$retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];}
				}
				foreach($retour AS $elementTraite)
				{
					if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total']))
					{$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;}
				}
			}
			else
			{
				$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;
			}
		}
	}
	else if ($classementSql === "WEEKDAY")
	{
		for ($compteur = 0 ; $compteur <= 6 ; $compteur++)
		{
			$retour[$compteur]['subdivision'] = $compteur;
			if (!empty($retourNonTraite))
			{
				foreach ($retourNonTraite AS $elementNonTraite)
				{
					if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision'])
					{$retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];$retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];$retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];}
				}
				foreach($retour AS $elementTraite)
				{
					if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total']))
					{$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;}
				}
			}
			else
			{
				$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;
			}
		}
	}
	else
	{
		//On supprime la fin de l'année si ça n'est pas encore passé
		if ($annee === (int) date('Y', time()))
		{$clauseRepetition = date('n', time()) - 1;}
		else
		{$clauseRepetition = 11;}

		for ($compteur = 0 ; $compteur <= $clauseRepetition ; $compteur++)
		{
			$retour[$compteur]['subdivision'] = $compteur;
			if (!empty($retourNonTraite))
			{
				foreach ($retourNonTraite AS $elementNonTraite)
				{
					if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision'])
					{$retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];$retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];$retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];}
				}
				foreach($retour AS $elementTraite)
				{
					if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total']))
					{$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;}
				}
			}
			else
			{
				$retour[$compteur]['nombre_inscriptions'] = 0;$retour[$compteur]['pourcentage_pour_division'] = 0;$retour[$compteur]['pourcentage_pour_total'] = 0;
			}
		}
	}
	return $retour;
}