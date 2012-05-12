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
 * Modèle contenant toutes les fonctions utiles à la gestion des réponses.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

function ListerReponses($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT version_id, version_date, version_commentaire, version_priorite, version_etat, " .
		"u1.utilisateur_id AS utilisateur_id, u1.utilisateur_pseudo AS utilisateur_pseudo, " .
		"groupe_class, u2.utilisateur_id AS id_admin, u2.utilisateur_pseudo AS pseudo_admin, " .
		"u1.utilisateur_avatar, cat_id, cat_nom " .
		"FROM zcov2_tracker_tickets_versions " .
		"LEFT JOIN zcov2_utilisateurs u1 ON version_id_utilisateur = u1.utilisateur_id " .
		"LEFT JOIN zcov2_utilisateurs u2 ON version_id_admin = u2.utilisateur_id " .
		"LEFT JOIN zcov2_categories ON version_id_categorie_concernee = cat_id " .
		"LEFT JOIN zcov2_groupes ON u1.utilisateur_id_groupe = groupe_id " .
		"WHERE version_id_ticket = :id " .
		"ORDER BY version_date");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	return $stmt->fetchAll();
}

function InfosReponse($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT version_id, version_date, version_id_ticket, version_commentaire, " .
			"ticket_titre, ticket_id_version_courante, utilisateur_id " .
			"utilisateur_id, utilisateur_pseudo " .
			"FROM zcov2_tracker_tickets_versions " .
			"LEFT JOIN zcov2_utilisateurs ON version_id_utilisateur = utilisateur_id " .
			"LEFT JOIN zcov2_tracker_tickets ON version_id_ticket = ticket_id " .
			"WHERE version_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function EditerReponseTicket($id, $texte)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_tracker_tickets_versions " .
			"SET version_commentaire = :texte " .
			"WHERE version_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':texte', $texte);
	$stmt->execute();
}

function AjouterReponse($id, $texte, $cat_concernee, $priorite, $etat, $id_assigne)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Insertion de la version
	$stmt = $dbh->prepare("INSERT INTO zcov2_tracker_tickets_versions(version_id_ticket, version_id_utilisateur, " .
			"version_id_categorie_concernee, " .
			"version_date, version_priorite, version_etat, version_id_admin, version_commentaire, version_ip) " .
			"VALUES(:id, :u, :cat_concernee, NOW(), :priorite, :etat, :assigne, :texte, :ip)");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':u', $_SESSION['id']);
	$stmt->bindParam(':cat_concernee', $cat_concernee);
	$stmt->bindParam(':priorite', $priorite);
	$stmt->bindParam(':assigne', $id_assigne);
	$stmt->bindParam(':texte', $texte);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp(true)));
	$stmt->bindParam(':etat', $etat);
	$stmt->execute();

	$id_v = $dbh->lastInsertId();
	$stmt->closeCursor();

	//Mise à jour du ticket
	$stmt = $dbh->prepare("UPDATE zcov2_tracker_tickets " .
			"SET ticket_id_version_courante = :id_v " .
			"WHERE ticket_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':id_v', $id_v);
	$stmt->execute();
	$stmt->closeCursor();

	Container::getService('zco_core.cache')->Delete('liste_tickets');
	\Container::getService('zco_admin.manager')->get('demandes', true);
	return $id_v;
}

function SupprimerReponse($id_v, $id_t, $maj_id_v)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Suppression de la réponse
	$stmt = $dbh->prepare("DELETE FROM zcov2_tracker_tickets_versions WHERE version_id = :id");
	$stmt->bindParam(':id', $id_v);
	$stmt->execute();

	//Mise à jour de l'id de version si nécessaire
	if($maj_id_v == true)
	{
		$stmt = $dbh->prepare("SELECT version_id " .
				"FROM zcov2_tracker_tickets_versions " .
				"WHERE version_id_ticket = :id " .
				"ORDER BY version_date DESC LIMIT 0,1");
		$stmt->bindParam(':id', $id_t);
		$stmt->execute();

		$id_v = $stmt->fetchColumn();

		$stmt = $dbh->prepare("UPDATE zcov2_tracker_tickets " .
				"SET ticket_id_version_courante = :id_v " .
				"WHERE ticket_id = :id_t");
		$stmt->bindParam(':id_v', $id_v);
		$stmt->bindParam(':id_t', $id_t);
		$stmt->execute();

		Container::getService('zco_core.cache')->Delete('liste_tickets');
		\Container::getService('zco_admin.manager')->get('demandes', true);
	}
}
