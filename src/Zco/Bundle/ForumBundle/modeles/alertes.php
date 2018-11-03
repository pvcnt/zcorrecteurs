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

/**
 * Modèle se chargeant des alertes (ajout, résolution, listage, etc.).
 *
 * @author DJ Fox, vincent1870
 * @begin 12/12/07
 * @last 02/12/08
 */

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
	$stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
	$stmt->execute();
}