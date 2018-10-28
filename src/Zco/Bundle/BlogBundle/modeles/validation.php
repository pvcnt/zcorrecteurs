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
 * Modèle gérant tout ce qui concerne le processus de validation d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 18/09/2007
 * @last 26/04/2009 vincent1870
 */

/**
 * Infos de validation sur une version.
 * @param integer $id			L'id de la version.
 * @return array
 */
function InfosValidationVersion($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT valid_commentaire, utilisateur_id, utilisateur_pseudo
	FROM zcov2_blog_validation
	LEFT JOIN zcov2_utilisateurs ON valid_id_utilisateur = utilisateur_id
	WHERE valid_id_version = :id
	ORDER BY valid_date DESC
	LIMIT 0, 1");
	$stmt->bindParam(':id', $id);

	$stmt->execute();

	$return = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $return;
}

/**
 * Ajoute une entrée dans l'historique de validation.
 * @param integer $id_billet			L'id du billet à proposer.
 * @param integer $id_version			L'id de la version proposée
 * @return void
 */
function AjouterHistoriqueValidation($id_billet, $id_u, $id_version, $texte, $decision)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_blog_validation(valid_id_billet, " .
			"valid_id_utilisateur, valid_id_version, valid_date, valid_ip, " .
			"valid_commentaire, valid_decision) " .
			"VALUES(:id_billet, :id_utilisateur, :id_version, NOW(), :ip, " .
			":commentaire, :decision)");
	$stmt->bindParam(':id_billet', $id_billet);
	$stmt->bindParam(':id_utilisateur', $id_u);
	$stmt->bindParam(':id_version', $id_version);
	$stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
	$stmt->bindParam(':commentaire', $texte);
	$stmt->bindParam(':decision', $decision);
	$stmt->execute();
	$stmt->closeCursor();
}

/**
 * Récupère l'historique de validation d'un billet.
 * @param integer $id		L'id du billet.
 * @return array
 */
function HistoriqueValidation($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT valid_id, valid_ip, valid_id_version, " .
			"valid_decision, valid_commentaire, valid_date, utilisateur_id, " .
			"utilisateur_pseudo " .
			"FROM zcov2_blog_validation " .
			"LEFT JOIN zcov2_utilisateurs ON valid_id_utilisateur = utilisateur_id " .
			"WHERE valid_id_billet = :id " .
			"ORDER BY valid_date");
	$stmt->bindParam(':id', $id);

	$stmt->execute();
	return $stmt->fetchAll();
}
