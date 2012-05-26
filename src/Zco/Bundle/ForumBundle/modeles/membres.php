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
 * Modèle concernant l'activité des membres sur le forum.
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 * @begin 28/06/07
 * @last 01/01/09
 */

// Messages par forum
function MessagesParForum()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Récupération
	$stmt = $dbh->prepare("
	SELECT cat_id, cat_nom, COUNT( message_id ) AS pourcentage
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE message_auteur = :user_id
	AND sujet_corbeille = 0
	GROUP BY cat_id
	ORDER BY pourcentage DESC
	");
	$stmt->bindParam(':user_id', $_GET['id']);

	$stmt->execute();

	$forums = $stmt->fetchAll();

	// Vérification des droits
	foreach($forums as $cle=>$valeur)
	{
		if(!verifier('voir_sujets', $valeur['cat_id']))
			unset($forums[$cle]);
	}

	return $forums;
}

function ListerSujetsParticipe($id, $PremierMess, $MessaAfficher)
{
	$add = '';
	if(!empty($_GET['id2']))
		$add = ' AND sujet_forum_id = :forum_id';
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("
	SELECT sujet_id, sujet_titre, sujet_dernier_message, sujet_reponses, sujet_annonce, sujet_sondage, sujet_ferme, sujet_resolu,
	sujet_auteur, message_auteur, RegardeurDeLaPage.lunonlu_message_id AS regardeur_dernier_message_lu, RegardeurDeLaPage.lunonlu_participe AS regardeur_participe, Ma.utilisateur_id_groupe AS sujet_auteur_groupe,
	Ma.utilisateur_id AS sujet_auteur_pseudo_existe, Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, cat_id, cat_nom,
	IFNULL(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo,
	IFNULL(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, message_auteur AS sujet_dernier_message_auteur_id,
	UNIX_TIMESTAMP(message_date) AS message_timestamp, sujet_date, message_date
	FROM zcov2_forum_lunonlu
	LEFT JOIN zcov2_forum_sujets ON lunonlu_sujet_id = sujet_id
	LEFT JOIN zcov2_forum_lunonlu RegardeurDeLaPage ON sujet_id = RegardeurDeLaPage.lunonlu_sujet_id AND RegardeurDeLaPage.lunonlu_utilisateur_id = :regardeur
	LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id
	LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_sujets.sujet_auteur = Ma.utilisateur_id
	LEFT JOIN zcov2_utilisateurs Mb ON zcov2_forum_messages.message_auteur = Mb.utilisateur_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE zcov2_forum_lunonlu.lunonlu_utilisateur_id = :user_id
	AND zcov2_forum_lunonlu.lunonlu_participe = 1 AND sujet_corbeille = 0 ".$add."
	ORDER BY sujet_annonce DESC, message_date DESC
	LIMIT ".$PremierMess." , ".$MessaAfficher);
	$stmt->bindParam(':user_id', $id);
	$stmt->bindParam(':regardeur', $_SESSION['id']);
	if(!empty($_GET['id2']))
		$stmt->bindValue(':forum_id', $_GET['id2']);

	$stmt->execute();

	$forums = $stmt->fetchAll();

	// Vérification des droits
	foreach($forums as $cle=>$valeur)
	{
		if(!verifier('voir_sujets', $valeur['cat_id']))
			unset($forums[$cle]);
	}

	return $forums;
}

function CompterSujetsParticipe($id)
{
	$add = '';
	if(!empty($_GET['id2']))
		$add = 'AND sujet_forum_id = :forum_id ';
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('SELECT COUNT(*) AS nb, sujet_forum_id '
		.'FROM zcov2_forum_lunonlu '
		.'LEFT JOIN zcov2_forum_sujets ON lunonlu_sujet_id = sujet_id '
		.'WHERE zcov2_forum_lunonlu.lunonlu_utilisateur_id = :user_id '
		.'AND zcov2_forum_lunonlu.lunonlu_participe AND sujet_corbeille = 0 '
		.$add.'GROUP BY sujet_forum_id');

	$stmt->bindParam(':user_id', $_GET['id']);
	if(!empty($_GET['id2']))
		$stmt->bindValue(':forum_id', $_GET['id2']);

	$stmt->execute();
	$rows = $stmt->fetchAll();

	$nb = 0;
	foreach($rows as &$row)
		// Vérification des droits
		if(verifier('voir_sujets', $row['sujet_forum_id']))
			$nb += $row['nb'];

	return $nb;
}

function MarquerForumsLus($lu)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($lu)
	{
		$stmt = $dbh->prepare('UPDATE '.Container::getParameter('database.prefix').'utilisateurs SET '
			.'utilisateur_derniere_lecture = CURRENT_TIMESTAMP '
			.'WHERE utilisateur_id = :id');
		$stmt->bindParam(':id', $_SESSION['id']);
		$stmt->execute();
	}
	else
	{
		$stmt = $dbh->prepare('UPDATE '.Container::getParameter('database.prefix').'utilisateurs SET '
			.'utilisateur_derniere_lecture = 0 '
			.'WHERE utilisateur_id = :id');
		$stmt->bindParam(':id', $_SESSION['id']);
		$stmt->execute();

		$stmt = $dbh->prepare('UPDATE '.Container::getParameter('database.prefix').'forum_lunonlu SET '
			.'lunonlu_message_id = 0 '
			.'WHERE lunonlu_utilisateur_id = :id');
		$stmt->bindParam(':id', $_SESSION['id']);
		$stmt->execute();
	}
}

function MarquerSujetLu($sujet, $lu = true)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($lu)
	{
		// Récupérer le dernier message
		$stmt = $dbh->prepare('SELECT sujet_dernier_message '
			.'FROM '.Container::getParameter('database.prefix').'forum_sujets '
			.'WHERE sujet_id = :sujet');
		$stmt->bindParam('sujet', $sujet);
		$stmt->execute();
		$dernierMessage = $stmt->fetchColumn();

		if(!$dernierMessage)
			return false; // Ne devrait pas arriver - le sujet n'existe pas

		$stmt = $dbh->prepare('INSERT INTO '.Container::getParameter('database.prefix').'forum_lunonlu '
			.'(lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id) '
			.'VALUES(:id, :sujet, :message)');
		$stmt->bindParam('id', $_SESSION['id']);
		$stmt->bindParam('sujet', $sujet);
		$stmt->bindParam('message', $dernierMessage);
		$stmt->catchErrors(false);
		$stmt->execute();
		if($stmt->errorCode() == 23000) // Duplicate record
		{
			$stmt = $dbh->prepare('UPDATE '.Container::getParameter('database.prefix').'forum_lunonlu '
				.'SET lunonlu_message_id = :message '
				.'WHERE lunonlu_sujet_id = :sujet AND '
				.'lunonlu_utilisateur_id = :id');
			$stmt->bindParam('id', $_SESSION['id']);
			$stmt->bindParam('sujet', $sujet);
			$stmt->bindParam('message', $dernierMessage);
			$stmt->execute();
		}
	}
	else
	{
		$stmt = $dbh->prepare('DELETE FROM '.Container::getParameter('database.prefix').'forum_lunonlu '
			.'WHERE lunonlu_sujet_id = :sujet AND '
			.'lunonlu_utilisateur_id = :id');
		$stmt->bindParam('id', $_SESSION['id']);
		$stmt->bindParam('sujet', $sujet);
		$stmt->execute();
	}
}

function MarquerDernierMessageLu($message_id, $sujet_id)
{
        $dbh = Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("UPDATE ".Container::getParameter('database.prefix')."forum_lunonlu
        SET lunonlu_message_id = :message_id
        WHERE lunonlu_sujet_id = :sujet_id AND lunonlu_utilisateur_id = :utilisateur_id");

        $stmt->bindParam(':message_id', $message_id);
        $stmt->bindParam(':sujet_id', $sujet_id);
        $stmt->bindParam(':utilisateur_id', $_SESSION['id']);
        $stmt->execute();
}

function DerniereLecture($id)
{
	if(verifier('connecte'))
	{
		$dbh = Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare("SELECT UNIX_TIMESTAMP(utilisateur_derniere_lecture) AS utilisateur_derniere_lecture FROM zcov2_utilisateurs WHERE utilisateur_id = :user_id");
		$stmt->bindParam(':user_id', $id);
		$stmt->execute();
		$resultat = $stmt->fetch(PDO::FETCH_OBJ);
		return $resultat->utilisateur_derniere_lecture;
	}
	else
		return 0;
}
