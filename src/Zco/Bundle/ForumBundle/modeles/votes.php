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
 * Modèle concernant tout le vote.
 *
 * @author DJ Fox, vincent1870
 * @begin 09/07/2007
 * @last 01/01/09
 */

function VerifierValiditeChoix($choix_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Vérification de l'existence du choix, ainsi que du droit de voter (sondage fermé).
	$stmt = $dbh->prepare("
	SELECT choix_id, sondage_ferme, sujet_corbeille
	FROM zcov2_forum_sondages_choix
	LEFT JOIN zcov2_forum_sondages ON zcov2_forum_sondages_choix.choix_sondage_id = zcov2_forum_sondages.sondage_id
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_sondages.sondage_id = zcov2_forum_sujets.sujet_sondage
	WHERE choix_id = :choix_id");
	$stmt->bindParam(':choix_id', $choix_id);

	$stmt->execute();

	$resultat = $stmt->fetch(PDO::FETCH_ASSOC);

	if((!empty($resultat['choix_id']) OR isset($_POST['blanc'])) AND !$resultat['sondage_ferme'] AND !$resultat['sujet_corbeille'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

function Voter($sondage_id, $choix_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On regarde si le membre concerné a déjà voté.
	$stmt = $dbh->prepare("
	SELECT vote_membre_id
	FROM zcov2_forum_sondages_votes
	WHERE vote_membre_id = :membre AND vote_sondage_id = :sondage");
	$stmt->bindParam(':membre', $_SESSION['id']);
	$stmt->bindParam(':sondage', $sondage_id);

	$stmt->execute();

	$resultat = $stmt->fetch(PDO::FETCH_ASSOC);

	if(!empty($resultat['vote_membre_id']))
	{
		$DejaVote = true;
	}
	else
	{
		$DejaVote = false;
	}

	//S'il n'a pas encore voté, on enregistre le vote
	if(!$DejaVote)
	{
		//On enregistre le vote
		$stmt = $dbh->prepare("INSERT INTO zcov2_forum_sondages_votes (vote_membre_id, vote_sondage_id, vote_choix, vote_date)
		VALUES (:user_id, :sondage_id, :choix_id, NOW())");
		$stmt->bindParam(':user_id', $_SESSION['id']);
		$stmt->bindParam(':sondage_id', $sondage_id);
		$stmt->bindParam(':choix_id', $choix_id);

		$stmt->execute();

		return true;
	}
	else
	{
		return false;
	}
}

function VerifierDejaVote($vote_membre_id)
{
	if(!empty($vote_membre_id))
	{
		return true;
	}
	else
	{
		return false;
	}
}