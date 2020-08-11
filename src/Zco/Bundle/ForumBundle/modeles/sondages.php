<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

function ListerLesVotants($sondage_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT vote_membre_id,
	COALESCE(utilisateur_pseudo, 'Anonyme') AS utilisateur_pseudo, utilisateur_id, utilisateur_id_groupe, groupe_class
	FROM zcov2_forum_sondages_votes
	LEFT JOIN zcov2_utilisateurs ON zcov2_forum_sondages_votes.vote_membre_id = zcov2_utilisateurs.utilisateur_id
	LEFT JOIN zcov2_groupes ON groupe_id = utilisateur_id_groupe
	WHERE vote_sondage_id = :sondage
	ORDER BY utilisateur_id_groupe DESC, utilisateur_pseudo ASC
	");
	$stmt->bindParam(':sondage', $sondage_id);
	$stmt->execute();
	return $stmt->fetchAll();
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

function ListerQuestions($lesondage)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT choix_id, choix_sondage_id, choix_texte
	FROM zcov2_forum_sondages_choix
	WHERE choix_sondage_id = :sond
	ORDER BY choix_id ASC
	");
	$stmt->bindParam(':sond', $lesondage);

	$stmt->execute();

	if($resultat = $stmt->fetchAll())
	{
		return $resultat;
	}
	else
	{
		return false;
	}
}

function ModifierSondage(&$InfosSondage, &$ListerQuestions, &$reponses)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Réinitialiser les votes ?
	if(isset($_POST['reinitialiser_votes']))
	{
		$stmt = $dbh->prepare('DELETE FROM zcov2_forum_sondages_votes WHERE vote_sondage_id = :sond');
		$stmt->bindParam(':sond', $InfosSondage['sondage_id']);
		$stmt->execute();
	}

	// Question
	$stmt = $dbh->prepare('UPDATE zcov2_forum_sondages SET '
		.'sondage_question = :question '
		.'WHERE sondage_id = :sond');
	$stmt->bindParam(':question', $_POST['question']);
	$stmt->bindParam(':sond', $InfosSondage['sondage_id']);
	$stmt->execute();

	// Préparation des requêtes
	$ajout = $dbh->prepare('INSERT INTO zcov2_forum_sondages_choix '
		.'(choix_sondage_id, choix_texte) '
		.'VALUES (:choix_sondage_id, :choix_texte)');
	$ajout->bindParam(':choix_sondage_id', $InfosSondage['sondage_id']);

	$modif = $dbh->prepare('UPDATE zcov2_forum_sondages_choix SET '
		.'choix_texte = :choix_texte '
		.'WHERE choix_id = :choix_id');
	$suppression = $dbh->prepare('DELETE FROM zcov2_forum_sondages_choix '
		.'WHERE choix_id = :choix_id');
	$suppr_votes_reponse = $dbh->prepare('DELETE FROM zcov2_forum_sondages_votes '
		.'WHERE vote_choix = :choix_id');

	// Enregistrement
	foreach($reponses as $i => &$rep)
	{
		// Nouvelle réponse
		if(!isset($ListerQuestions[$i]))
		{
			$ajout->bindParam(':choix_texte', $rep);
			$ajout->execute();
		}

		// Réponse existante
		else
		{
			$ListerQuestions[$i]['modifiee'] = true;
			$modif->bindParam(':choix_texte', $rep);
			$modif->bindParam(':choix_id', $ListerQuestions[$i]['choix_id']);
			$modif->execute();
		}
	}

	// Réponses supprimées
	foreach($ListerQuestions as &$rep)
	{
		if(isset($rep['modifiee']))
			continue; // Réponse modifiée à l'étape précédente

		$suppression->bindParam(':choix_id', $rep['choix_id']);
		$suppression->execute();

		$suppr_votes_reponse->bindParam(':choix_id', $rep['choix_id']);
		$suppr_votes_reponse->execute();
	}
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

function CreerSondage(&$reponses)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On crée le sondage
	$stmt = $dbh->prepare('INSERT INTO zcov2_forum_sondages(sondage_question) VALUES (:sondage_question)');
	$stmt->bindParam(':sondage_question', $_POST['sondage_question']);
	$stmt->execute();

	//On récupère l'id de l'enregistrement qui vient d'être créé (l'id du sondage).
	$nouveau_sondage_id = $dbh->lastInsertId();

	//On ajoute les choix
	foreach($reponses as &$rep)
	{
		$stmt = $dbh->prepare('INSERT INTO zcov2_forum_sondages_choix '
			.'(choix_sondage_id, choix_texte) '
			.'VALUES (:choix_sondage_id, :choix_texte)');
		$stmt->bindParam(':choix_sondage_id', $nouveau_sondage_id);
		$stmt->bindParam(':choix_texte',  $rep);
		$stmt->execute();
	}
	return $nouveau_sondage_id;
}

function CreerSondageSujet($sujet, &$reponses)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$nouveau_sondage_id = CreerSondage($reponses);

	//On update le sujet
	$stmt = $dbh->prepare('UPDATE zcov2_forum_sujets SET '
		.'sujet_sondage = :sond '
		.'WHERE sujet_id = :sujet');
	$stmt->bindValue(':sond', $nouveau_sondage_id);
	$stmt->bindParam(':sujet', $sujet);
	$stmt->execute();

	$stmt->closeCursor();

	return true;
}
