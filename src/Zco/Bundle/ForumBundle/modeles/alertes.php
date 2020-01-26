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

/**
 * Modèle se chargeant des alertes (ajout, résolution, listage, etc.).
 *
 * @author DJ Fox, vincent1870
 * @begin 12/12/07
 * @last 02/12/08
 */

/**
 * Récupère la liste des alertes sur un sujet (ou sur tout le forum).
 * @param integer $id				L'id du sujet (si aucun, tout le forum).
 * @return array
 */
function ListerAlertes($id = null)
{
	//Récupération des alertes
	$dbh = Doctrine_Manager::connection()->getDbh();
	if(isset($_GET['solved']) && is_numeric($_GET['solved']) && in_array($_GET['solved'], array(0, 1)))
		$where = '= '.$_GET['solved'];
	else
		$where = 'IN(0, 1)';
	if(!is_null($id))
		$where.= ' AND alerte_sujet_id = '.$id;

	$stmt = $dbh->prepare("SELECT alerte_id, alerte_raison, alerte_ip, alerte_resolu, alerte_date, " .
	"sujet_titre, sujet_id, sujet_resolu, sujet_ferme, sujet_corbeille, sujet_forum_id, " .
	"g1.groupe_class AS groupe_class_membre, g2.groupe_class AS groupe_class_admin " .
	"u1.utilisateur_id AS membre_id, COALESCE(u1.utilisateur_pseudo, 'Anonyme') AS membre_pseudo, " .
	"u2.utilisateur_id AS admin_id, COALESCE(u2.utilisateur_pseudo, 'Anonyme') AS admin_pseudo " .
	"FROM zcov2_forum_alertes " .
	"LEFT JOIN zcov2_utilisateurs u1 ON alerte_auteur = u1.utilisateur_id " .
	"LEFT JOIN zcov2_groupes g1 ON u1.utilisateur_id_groupe = g1.groupe_id " .
	"LEFT JOIN zcov2_utilisateurs u2 ON alerte_id_admin = u2.utilisateur_id " .
	"LEFT JOIN zcov2_groupes g2 ON u2.utilisateur_id_groupe = g2.groupe_id " .
	"LEFT JOIN zcov2_forum_sujets ON alerte_sujet_id = sujet_id " .
	"WHERE alerte_resolu ".$where." " .
	"ORDER BY alerte_resolu ASC, alerte_date DESC");
	$stmt->bindParam(':s', $id);
	$stmt->execute();
	$alertes = $stmt->fetchAll();

	//Filtrage selon les droits
	if(is_null($id))
	{
		foreach($alertes as $cle=>$valeur)
		{
			if(!verifier('voir_alertes', $valeur['sujet_forum_id']))
				unset($alertes[$cle]);
		}
	}

	//Création de l'array des modos
	$current = null;
	foreach($alertes as $cle=>$valeur)
	{
		if($current != $valeur['alerte_id'])
		{
			$current = $valeur['alerte_id'];
		}
		else
		{
			unset($alertes[$cle]);
		}
	}

	return $alertes;
}

/**
 * Marquer une alerte comme résolue.
 * @param integer $id				L'id de l'alerte.
 * @return void
 */
function AlerteResolue($id, $id_u)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_forum_alertes " .
			"SET alerte_resolu = 1, alerte_id_admin = :u " .
			"WHERE alerte_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':u', $id_u);
	$stmt->execute();
}

/**
 * Marquer une alerte comme non résolue.
 * @param integer $id				L'id de l'alerte.
 * @return void
 */
function AlerteNonResolue($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_forum_alertes " .
			"SET alerte_resolu = 0 " .
			"WHERE alerte_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
 * Retourne le nombre d'alertes non résolues.
 * @return integer
 */
function CompterAlertes()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_forum_alertes
	WHERE alerte_resolu = 0");
	$stmt->execute();
	return $stmt->fetchColumn();
}

/**
 * Retourne le nombre total d'alertes.
 * @return integer
 */
function CompterTotalAlertes()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_forum_alertes");
	$stmt->execute();
	return $stmt->fetchColumn();
}

/**
 * Vérifie si on a le droit d'alerter sur un sujet (pas d'alerte en cours).
 * @param integer $id					L'id du sujet.
 * @return boolean
 */
function VerifierAutorisationAlerter($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT COUNT(alerte_id) AS nb
	FROM zcov2_forum_alertes
	WHERE alerte_sujet_id = :s AND alerte_resolu = 0");
	$stmt->bindParam(':s', $id);
	$stmt->execute();
	return ($stmt->fetchColumn() > 0 ? false : true);
}

//Ajoute une nouvelle alerte
function EnregistrerNouvelleAlerte($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On crée l'alerte
	$stmt = $dbh->prepare("
	INSERT INTO zcov2_forum_alertes (alerte_id, alerte_auteur, alerte_sujet_id, alerte_date, alerte_raison, alerte_resolu, alerte_ip)
	VALUES ('', :id_utilisateur, :id_sujet, NOW(), :texte, 0, :ip)");
	$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
	$stmt->bindParam(':id_sujet', $id);
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp(true)));
	$stmt->execute();
}

/**
 * Récupère la liste des alertes, formatée pour l'afficher dans un flux.
 * @param integer $id_groupe	L'id du groupe dotn on va vérifier les droits.
 * @return array
 */
function ListerAlertesFlux($id_groupe)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Récupération des alertes
	$stmt = $dbh->prepare("SELECT alerte_id, alerte_raison, " .
	"UNIX_TIMESTAMP(alerte_date) as pubtime, utilisateur_email, " .
	"sujet_titre, sujet_id, sujet_forum_id, utilisateur_id, utilisateur_pseudo  " .
	"FROM zcov2_forum_alertes " .
	"LEFT JOIN zcov2_utilisateurs ON alerte_auteur = utilisateur_id " .
	"LEFT JOIN zcov2_forum_sujets ON alerte_sujet_id = sujet_id " .
	"WHERE alerte_resolu = 0 " .
	"ORDER BY alerte_date DESC");
	$stmt->execute();
	$alertes = $stmt->fetchAll();

	$latest = array('pubtime' => 0);

	//Filtrage selon les droits
	foreach($alertes as $cle => &$valeur)
	{
		if(!verifier('voir_alertes', $valeur['sujet_forum_id'], $id_groupe))
			unset($alertes[$cle]);
		elseif($valeur['pubtime'] > $latest['pubtime'])
			$latest = &$valeur;
	}

	return array($latest, $alertes);
}
