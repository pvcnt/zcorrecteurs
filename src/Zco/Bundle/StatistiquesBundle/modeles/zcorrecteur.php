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
 * Modèle gérant la récupération des statistiques de zCorrection par zCorrecteur.
 *
 * @package zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 05/12/2007
 * @last 14/08/2008 vincent1870
 */

/**
 * Retourne le rapport d'activité d'un zCorrecteur.
 * @param integer $id					L'id de l'utilisateur.
 * @return array
 */
function RecupStatistiquesZcorrecteur($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Récupération des infos sur le zCorrecteur
	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_id_groupe, utilisateur_avatar
	FROM zcov2_utilisateurs
	WHERE utilisateur_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$infos = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	//Calcul du nombre total de mini-tutos
	//->corrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_1
	WHERE correction_id_correcteur=:id AND correction_date_fin IS NOT NULL AND soumission_type_tuto=".MINI_TUTO);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_corrections_mini_tutos = $stmt->fetchColumn();
	$stmt->closeCursor();

	//->recorrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_2
	WHERE correction_id_correcteur=:id AND correction_date_fin IS NOT NULL AND soumission_type_tuto=".MINI_TUTO);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_recorrections_mini_tutos = $stmt->fetchColumn();
	$stmt->closeCursor();

	//Calcul du nombre total de big-tutos
	//->corrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_1
	WHERE correction_id_correcteur=:id AND correction_date_fin IS NOT NULL AND soumission_type_tuto=".BIG_TUTO);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_corrections_big_tutos = $stmt->fetchColumn();
	$stmt->closeCursor();

	//->recorrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_2
	WHERE correction_id_correcteur=:id AND correction_date_fin IS NOT NULL AND soumission_type_tuto=".BIG_TUTO);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_recorrections_big_tutos = $stmt->fetchColumn();
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
	WHERE correction_id_correcteur=:id AND correction_abandonee=0");
	$stmt->bindParam(':id', $id);
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
	WHERE soumission_type_tuto = :type_tuto AND correction_id_correcteur=:id AND correction_abandonee=0");
	$mini_tuto = MINITUTO;
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':type_tuto', $mini_tuto);
	$stmt->execute();
	$temps_moyen_correction_mini = $stmt->fetchColumn();
	$stmt->closeCursor();

	//Calcul du temps moyen de correction de big-tutos
	$big_tuto = BIGTUTO;
	$stmt->bindParam(':id', $id);
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
	WHERE correction_id_correcteur=:id AND correction_abandonee=0");
	$stmt->bindParam(':id', $id);
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
	WHERE soumission_type_tuto = :type_tuto AND correction_id_correcteur=:id AND correction_abandonee=0");
	$mini_tuto = MINITUTO;
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':type_tuto', $mini_tuto);
	$stmt->execute();
	$temps_moyen_recorrection_mini = $stmt->fetchColumn();
	$stmt->closeCursor();

	//Calcul du temps moyen de REcorrection de big-tutos
	$big_tuto = BIGTUTO;
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':type_tuto', $big_tuto);
	$stmt->execute();
	$temps_moyen_recorrection_big = $stmt->fetchColumn();
	$stmt->closeCursor();

	//Calcul du nombre total de tutos sur les 12 derniers mois
	//->corrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_1
	WHERE correction_id_correcteur=:id AND correction_date_fin >= NOW() - INTERVAL 12 MONTH");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_corrections_12_mois = $stmt->fetchColumn();
	$stmt->closeCursor();

	//->recorrections
	$stmt = $dbh->prepare("
	SELECT COUNT(soumission_id) AS nombre_total_tutos
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections ON correction_id=soumission_id_correction_2
	WHERE correction_id_correcteur=:id AND correction_date_fin >= NOW() - INTERVAL 12 MONTH");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$nombre_recorrections_12_mois = $stmt->fetchColumn();
	$stmt->closeCursor();

	//Retour des résultats dans un tableau associatif
	return array(
		'infos' => $infos,
		'nombre_corrections_mini_tutos' => $nombre_corrections_mini_tutos,
		'nombre_recorrections_mini_tutos' => $nombre_recorrections_mini_tutos,
		'nombre_total_mini_tutos' => ($nombre_corrections_mini_tutos + $nombre_recorrections_mini_tutos),
		'nombre_corrections_big_tutos' => $nombre_corrections_big_tutos,
		'nombre_recorrections_big_tutos' => $nombre_recorrections_big_tutos,
		'nombre_total_big_tutos' => ($nombre_corrections_big_tutos + $nombre_recorrections_big_tutos),
		'temps_moyen_correction_global' => $temps_moyen_correction_global,
		'temps_moyen_correction_mini' => $temps_moyen_correction_mini,
		'temps_moyen_correction_big' => $temps_moyen_correction_big,
		'temps_moyen_recorrection_global' => $temps_moyen_recorrection_global,
		'temps_moyen_recorrection_mini' => $temps_moyen_recorrection_mini,
		'temps_moyen_recorrection_big' => $temps_moyen_recorrection_big,
		'nombre_corrections' => ($nombre_corrections_mini_tutos + $nombre_corrections_big_tutos),
		'nombre_recorrections' => ($nombre_recorrections_mini_tutos + $nombre_recorrections_big_tutos),
		'nombre_total_corrections' => ($nombre_recorrections_mini_tutos + $nombre_recorrections_big_tutos + $nombre_corrections_mini_tutos + $nombre_corrections_big_tutos),
		'nombre_corrections_12_mois' => $nombre_corrections_12_mois,
		'nombre_recorrections_12_mois' => $nombre_recorrections_12_mois,
		'nombre_total_corrections_12_mois' => ($nombre_corrections_12_mois + $nombre_recorrections_12_mois)
	);
}
