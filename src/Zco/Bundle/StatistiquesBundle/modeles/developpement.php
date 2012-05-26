<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

/*
 * Modèle s'occupant des statistiques concernant le développement du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 26/03/2007
 * @last 26/03/2009 vincent1870
 */

/**
 * Récupère le nombre de demandes résolues par développeur.
 * @param integer $annee				L'année concernée pour les stats.
 * @return array
 */
function RecupRapportDeveloppeursAnnee($annee)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$devs = array();

	$ListerEquipe = ListerUtilisateursDroit('tracker_etre_assigne');
	foreach($ListerEquipe as $mbr)
		$devs[] = (int)$mbr['utilisateur_id'];
	$devs = implode(',', $devs);

	$stmt = $dbh->prepare(
		'SELECT 0 AS type, ' // Informations sur le membre (groupe etc)
			.'NULL AS nb, utilisateur_id, utilisateur_pseudo, '
			.'utilisateur_id_groupe, 0 AS mois '
			.'FROM zcov2_utilisateurs '
			.'WHERE utilisateur_id IN('.$devs.') '
		.'UNION '
		.'SELECT 1 AS type, ' // Anomalies
			.'COUNT(*) AS nb, utilisateur_id, NULL AS utilisateur_pseudo, '
			.'utilisateur_id_groupe, MONTH(version_date) - 1 AS mois '
			.'FROM zcov2_tracker_tickets '
			.'LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id '
			.'LEFT JOIN zcov2_utilisateurs ON version_id_admin = utilisateur_id '
			.'WHERE utilisateur_id IN('.$devs.') '
			.'AND ticket_type = \'bug\' '
			.'AND version_etat IN(4,5,7,8) '
			.'AND YEAR(version_date) = :annee '
			.'GROUP BY utilisateur_id, MONTH(version_date) '
		.'UNION '
		.'SELECT 2 AS type, ' // Tâches
			.'COUNT(*) AS nb, utilisateur_id, NULL AS utilisateur_pseudo, '
			.'utilisateur_id_groupe, MONTH(version_date) - 1 AS mois '
			.'FROM zcov2_tracker_tickets '
			.'LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id '
			.'LEFT JOIN zcov2_utilisateurs ON version_id_admin = utilisateur_id '
			.'WHERE utilisateur_id IN('.$devs.') '
			.'AND ticket_type = \'tache\' '
			.'AND version_etat IN(4,5,7,8) '
			.'AND YEAR(version_date) = :annee '
			.'GROUP BY utilisateur_id, MONTH(version_date) '
		.'ORDER BY utilisateur_id, mois, type'
	);
	$stmt->bindParam(':annee', $annee);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stats = array();

	foreach($data as &$row)
	{
		$type = $row['type'];
		unset($row['type']);

		if($type == 0)
		{
			unset($row['nb']);
			$stats[$row['utilisateur_id']] = $row;
			$stats[$row['utilisateur_id']]['mois'] = array();
		}
		elseif($type == 1) // Anomalies
			$stats[$row['utilisateur_id']]['mois'][$row['mois']]['anomalies'] = (int)$row['nb'];
		elseif($type == 2) // Tâches
			$stats[$row['utilisateur_id']]['mois'][$row['mois']]['taches'] = (int)$row['nb'];
	}

	foreach($stats as $k => &$dev)
	{
		$m = date('m');
		for($i = 0; $i < $m; $i++)
			if(!isset($dev['mois'][$i]) || count($dev['mois'][$i]) != 3)
				$dev['mois'][$i] = array(
					'anomalies'	=> isset($dev['mois'][$i]['anomalies']) ? $dev['mois'][$i]['anomalies'] : 0,
					'taches'	=> isset($dev['mois'][$i]['taches']) ? $dev['mois'][$i]['taches'] : 0,
					'total'		=> 0
				);
		$dev['anomalies'] = $dev['taches'] = 0;
		foreach($dev['mois'] as &$mois)
		{
			$mois['total'] = $mois['anomalies'] + $mois['taches'];
			$dev['anomalies'] += $mois['anomalies'];
			$dev['taches'] += $mois['taches'];
		}
		$dev['total'] = $dev['anomalies'] + $dev['taches'];

		if(!$dev['total']) unset($stats[$k]);
	}

	$cmp = create_function('$a, $b', 'if ($a["total"] == $b["total"]) '
		.' return strtolower($a["utilisateur_pseudo"]) '
		.' < strtolower($b["utilisateur_pseudo"]) ? -1 : 1; '
		.' return $a["total"] < $b["total"] ? 1 : -1;');
	uasort($stats, $cmp);
	return $stats;
}

function RecupRapportDeveloppeursMois($annee, $mois)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$devs = array();

	$ListerEquipe = ListerUtilisateursDroit('tracker_etre_assigne');
	foreach($ListerEquipe as $mbr)
		$devs[] = (int)$mbr['utilisateur_id'];
	$devs = implode(',', $devs);

	$stmt = $dbh->prepare(
		'SELECT 0 AS type, ' // Informations sur le membre (groupe etc)
			.'NULL AS nb, utilisateur_id, utilisateur_pseudo, '
			.'groupe_class, groupe_logo, groupe_nom '
			.'FROM zcov2_utilisateurs '
			.'LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id '
			.'WHERE utilisateur_id IN('.$devs.') '
		.'UNION '
		.'SELECT 1 AS type, ' // Anomalies
			.'COUNT(*) AS nb, utilisateur_id, NULL AS utilisateur_pseudo, '
			.'NULL AS groupe_class, NULL AS groupe_logo, NULL AS groupe_nom '
			.'FROM zcov2_tracker_tickets '
			.'LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id '
			.'LEFT JOIN zcov2_utilisateurs ON version_id_admin = utilisateur_id '
			.'WHERE utilisateur_id IN('.$devs.') '
			.'AND ticket_type = \'bug\' '
			.'AND version_etat IN(4,5,7,8) '
			.'AND YEAR(version_date) = :annee '
			.'AND MONTH(version_date) = :mois '
			.'GROUP BY utilisateur_id '
		.'UNION '
		.'SELECT 2 AS type, ' // Tâches
			.'COUNT(*) AS nb, utilisateur_id, NULL AS utilisateur_pseudo, '
			.'NULL AS groupe_class, NULL AS groupe_logo, NULL AS groupe_nom '
			.'FROM zcov2_tracker_tickets '
			.'LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id '
			.'LEFT JOIN zcov2_utilisateurs ON version_id_admin = utilisateur_id '
			.'WHERE utilisateur_id IN('.$devs.') '
			.'AND ticket_type = \'tache\' '
			.'AND version_etat IN(4,5,7,8) '
			.'AND YEAR(version_date) = :annee '
			.'AND MONTH(version_date) = :mois '
			.'GROUP BY utilisateur_id '
		.'ORDER BY utilisateur_id, type'
	);
	$stmt->bindParam(':annee', $annee);
	$stmt->bindParam(':mois', $mois);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stats = array();

	foreach($data as &$row)
	{
		$type = $row['type'];
		unset($row['type']);

		if($type == 0)
		{
			unset($row['nb']);
			$stats[$row['utilisateur_id']] = $row;
			$stats[$row['utilisateur_id']]['anomalies'] = 0;
			$stats[$row['utilisateur_id']]['taches'] = 0;
		}
		elseif($type == 1) // Anomalies
			$stats[$row['utilisateur_id']]['anomalies'] = (int)$row['nb'];
		elseif($type == 2) // Tâches
			$stats[$row['utilisateur_id']]['taches'] = (int)$row['nb'];
	}
	foreach($stats as &$dev)
		$dev['total'] = $dev['anomalies'] + $dev['taches'];

	$cmp = create_function('$a, $b', 'if ($a["total"] == $b["total"]) '
		.' return strtolower($a["utilisateur_pseudo"]) '
		.' < strtolower($b["utilisateur_pseudo"]) ? -1 : 1; '
		.' return $a["total"] < $b["total"] ? 1 : -1;');
	uasort($stats, $cmp);
	return $stats;
}
