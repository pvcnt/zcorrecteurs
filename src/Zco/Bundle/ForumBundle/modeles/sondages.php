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
 * Modèle s'occupant des sondages.
 *
 * @author DJ Fox, vincent1870
 * @begin juillet 2007
 * @last 01/01/09
 */

function ListerResultatsSondage($sondage_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$retour = array();

	//Votes normaux
	$stmt = $dbh->prepare("
	SELECT choix_id, choix_texte, COUNT(vote_choix) AS nombre_votes
	FROM zcov2_forum_sondages_choix
	LEFT JOIN zcov2_forum_sondages_votes ON vote_choix = choix_id
	WHERE choix_sondage_id = :sondage
	GROUP BY choix_id
	ORDER BY choix_id ASC
	");
	$stmt->bindParam(':sondage', $sondage_id);
	$stmt->execute();
	$retour = $stmt->fetchAll();
	$stmt->closeCursor();

	//Votes blancs
	$stmt = $dbh->prepare("
	SELECT COUNT(vote_choix) AS nombre_votes
	FROM zcov2_forum_sondages_votes
	WHERE vote_sondage_id = :sondage AND vote_choix = 0
	");
	$stmt->bindParam(':sondage', $sondage_id);
	$stmt->execute();
	$retour[] = array('nombre_votes'=>$stmt->fetchColumn(), 'choix_id'=>0, 'choix_texte'=>'Vote blanc');
	$stmt->closeCursor();

	return $retour;
}

function InfosSondage($lesondage)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT sondage_id, sondage_question, sujet_id, " .
			"sujet_titre, sujet_sondage, cat_id, cat_nom " .
			"FROM zcov2_forum_sondages " .
			"LEFT JOIN zcov2_forum_sujets ON sondage_id = sujet_sondage " .
			"LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
			"WHERE sondage_id = :sond");
	$stmt->bindParam(':sond', $lesondage);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function SupprimerSondage($sond)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On supprime le sondage du sujet.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages
	WHERE sondage_id = :sondage_id");
	$stmt->bindParam(':sondage_id', $sond);

	$stmt->execute();


	//On supprime les choix du sondage
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_choix
	WHERE choix_sondage_id = :choix_sondage_id");
	$stmt->bindParam(':choix_sondage_id', $sond);

	$stmt->execute();


	//On supprime les votes du sondage
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_votes
	WHERE vote_sondage_id = :vote_sondage_id");
	$stmt->bindParam(':vote_sondage_id', $sond);

	$stmt->execute();

	//On update le sujet
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_sondage = :zero
	WHERE sujet_sondage = :sondage_id");
	$stmt->bindValue(':zero', 0);
	$stmt->bindParam(':sondage_id', $sond);
	$stmt->execute();

	return true;
}