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

/**
 * Contrôleur gérant les options des membres.
 *
 * @author vincent1870, Dj Fox
 */

/**
 * Modifie les options de navigation d'un membre.
 * 
 * @param integer $id L'id de l'utilisateur.
 */
function ModifierOptionsNavigation($id, array $preferences)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$set = array();
	foreach (array_keys($preferences) as $pref)
	{
	    $set[] = 'preference_'.$pref.' = :'.$pref;
	}

	$stmt = $dbh->prepare('UPDATE zcov2_utilisateurs_preferences '.
	'SET '.implode(', ', $set).' '.
	'WHERE preference_id_utilisateur = :id');
    
    foreach ($preferences as $key => $value)
    {
        $stmt->bindValue(':'.$key, $value);
    }

	$stmt->bindValue(':id', $id);
	$stmt->execute();

	//Modification des options en session si on modifie son profil
	if ($id == $_SESSION['id'])
	{
	    foreach ($preferences as $key => $value)
	    {
		    $_SESSION['prefs'][$key] = $value;
	    }
	}
}

/**
 * Récupère les options de navigation d'un membre.
 * @param integer $id					L'id de l'utilisateur.
 * @return array
 */
function RecupererOptionsNavigation($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT * " .
			"FROM zcov2_utilisateurs_preferences " .
			"WHERE preference_id_utilisateur = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Active une nouvelle adresse mail.
 * @param string $hash				Le hash de validation.
 * @return array					Un tableau de type array(0 => nouveau_mail, 1 => id_utilisateur).
 */
function ActiverNouveauMail($hash)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Si on a besoin de validation
	$stmt = $dbh->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_email = utilisateur_nouvel_email
	WHERE utilisateur_hash_validation2 = :hash");
	$stmt->bindParam(':hash', $hash);
	$stmt->execute();

	$stmt = $dbh->prepare("
	SELECT utilisateur_id, utilisateur_nouvel_email
	FROM zcov2_utilisateurs
	WHERE utilisateur_hash_validation2 = :hash");
	$stmt->bindParam(':hash', $hash);
	$stmt->execute();
	$infos = $stmt->fetch(PDO::FETCH_ASSOC);

	return array($infos['utilisateur_nouvel_email'], $infos['utilisateur_id']);
}

// Efface la période d'absence d'un membre
function SupprimerAbsence($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_fin_absence = null, utilisateur_absent = 0, utilisateur_motif_absence = '', utilisateur_debut_absence = null
	WHERE utilisateur_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
 *	Fonction qui ajoute une absence à un membre
 *
 *	integer $id 		ID du membre
 *	array $post		debut_abs = date de début de l'absence (jj/mm/aaaa)
 *							fin_abs = date de fin de l'absence (jj/mm/aaaa)
 *							duree_abs = durée de l'absence (en mois, jour, année)
 *							temps_abs = durée de l'absence (nombre de jour, mois, année)
 * return mixed		1 = date non valide
 *							2 = date de fin antérieur à la date de début
 *							true = opération effectuée avec succés
**/
function AjouterAbsence($id, $post)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$add = array();
	$array_duree = array('', 'DAY', 'MONTH', 'YEAR');
	$date_debut = 'NOW()';
	$absence = 1;

	// Si une date de début est saisie, on la prend en compte
	if(!empty($post['debut_abs']))
	{
		// On vérifie la validité de la date, si c'est valide, on l'utilise, sinon date d'aujourd'hui
		$date_debut = explode('/', $post['debut_abs']);
		if(count($date_debut)==3 && checkdate($date_debut[1], $date_debut[0], $date_debut[2]))
		{
			// On compare les dates, si la date du jour est antérieure à la date saisie, on n'indique pas l'absence
			$prem_date = date("Y").date("m").date("d");
			$deux_date = $date_debut[2].$date_debut[1].$date_debut[0];
			if($prem_date<$deux_date)
				$absence = 0;

			$date_debut = "STR_TO_DATE('".implode('/', $date_debut)."', '%d/%m/%Y')";
			$add[] = "utilisateur_debut_absence = ".$date_debut;
		}
		else
			return 1;
	}
	else
	{
		$add[] = 'utilisateur_debut_absence = '.$date_debut;
	}

	// Pour la fin de l'absence : si on a saisi la date en dur
	if(!empty($post['fin_abs']))
	{
		// On vérifie la validité de la date
		$date_fin = explode('/', $post['fin_abs']);
		if(count($date_fin)==3 && checkdate($date_fin[1], $date_fin[0], $date_fin[2]))
		{
			// On vérifie que la date de fin est postérieure à la date de début
			if($date_debut=="NOW()"){
				$prem_date = date("Y").date("m").date("d");
			} else {
				$prem_date = explode('/', $post['debut_abs']);
				$prem_date = $prem_date[2].$prem_date[1].$prem_date[0];
			}
			$deux_date = $date_fin[2].$date_fin[1].$date_fin[0];
			if($prem_date>$deux_date)
				return 2;

			$add[] = "utilisateur_fin_absence = STR_TO_DATE('".$post['fin_abs']."', '%d/%m/%Y')";
			$temps = null;
		}
		else
			return 1;
	}
	else	if($post['duree_abs']==0) // Sinon on utilise l'autre champ
	{
		$add[] = "utilisateur_fin_absence = null";
		$temps = null;
	}
	else
	{
		$add[] = 'utilisateur_fin_absence = '.$date_debut.' + INTERVAL :temps '.$array_duree[$post['duree_abs']];
		$temps = $post['temps_abs'];
	}

	$stmt = $db->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_motif_absence = :texte, utilisateur_absent = :absence, ".implode(", ", $add)."
	WHERE utilisateur_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':texte', $post['texte']);
	$stmt->bindParam(':absence', $absence);
	($temps!=null) && $stmt->bindParam(':temps', $temps);

	$stmt->execute();

	return true;
}

/**
 * Liste les sauvegardes zCode d'un membre.
 * @param integer $id					L'id de l'utilisateur.
 * @return array
 */
function ListerSauvegardesZcode($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT sauvegarde_id, sauvegarde_url, sauvegarde_texte, sauvegarde_date " .
			"FROM zcov2_sauvegardes_zform " .
			"WHERE sauvegarde_id_utilisateur = :id " .
			"ORDER BY sauvegarde_date DESC");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchAll();
}
