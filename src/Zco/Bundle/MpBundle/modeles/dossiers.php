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
// +----------------------------------------------------------------------+
// | Copyright (c) www.zcorrecteurs.fr 2008                               |
// +----------------------------------------------------------------------+
// | Modèle concernant les dossiers des MP                                |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 04 septembre 2008                         |
// | Dernière modification le : 04 septembre 2008                         |
// +----------------------------------------------------------------------+

function ListerDossiers()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_dossier_id, mp_dossier_titre, COUNT(mp_participant_mp_id) AS nombre_dans_dossier
	FROM zcov2_mp_dossiers
	LEFT JOIN zcov2_mp_participants ON mp_dossier_id = mp_participant_mp_dossier_id
	WHERE mp_dossier_auteur_id = :id AND (mp_participant_statut > -1 OR mp_participant_statut IS NULL)
	GROUP BY mp_dossier_id
	ORDER BY mp_dossier_titre");
	$stmt->bindParam(':id', $_SESSION['id']);

	$stmt->execute();

	return $stmt->fetchAll();
}

function AjouterDossier()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_dossiers (mp_dossier_auteur_id, mp_dossier_titre) VALUES (:user_id, :dossier_nom)");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':dossier_nom', $_POST['dossier_nom']);

	$stmt->execute();

	return true;
}

function DossierExiste()
{
	if(!empty($_GET['id']) AND is_numeric($_GET['id']))
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("
		SELECT mp_dossier_id, mp_dossier_titre
		FROM zcov2_mp_dossiers
		WHERE mp_dossier_auteur_id = :user AND mp_dossier_id = :folder");
		$stmt->bindParam(':user', $_SESSION['id']);
		$stmt->bindParam(':folder', $_GET['id']);

		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		return false;
	}
}

function RenommerDossier()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_dossiers SET mp_dossier_titre = :dossier_nom
	WHERE mp_dossier_id = :dossier_id AND mp_dossier_auteur_id = :user_id");
	$stmt->bindParam(':dossier_nom', $_POST['dossier_nom']);
	$stmt->bindParam(':dossier_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_SESSION['id']);

	$stmt->execute();

	return true;
}

function SupprimerDossier()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	DELETE FROM zcov2_mp_dossiers
	WHERE mp_dossier_id = :dossier_id AND mp_dossier_auteur_id = :user_id");
	$stmt->bindParam(':dossier_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_SESSION['id']);

	$stmt->execute();

	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_participants SET mp_participant_mp_dossier_id = 0
	WHERE mp_participant_mp_dossier_id = :dossier_id AND mp_participant_id = :user_id");
	$stmt->bindParam(':dossier_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_SESSION['id']);

	$stmt->execute();

	return true;
}
