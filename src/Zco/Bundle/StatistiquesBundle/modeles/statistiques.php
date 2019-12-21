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
 * Modèle s'occupant des statistiques globales du site.
 *
 * @author DJ Fox, Ziame
 * @begin 14/11/2007
 * @last 26/03/2009 vincent1870
 */

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