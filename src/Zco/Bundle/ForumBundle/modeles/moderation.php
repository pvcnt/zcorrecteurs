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
 * Modèle s'occupant des opérations de modération.
 *
 * @author DJ Fox, vincent1870
 * @begin juillet 2007
 * @last 13/01/09 Barbatos
 */

/**
 * Change le statut résolu d'un sujet.
 * @param integer $sujet_id				L'id du sujet.
 * @param boolean $resolu_actuel		Le statut résolu actuel.
 * @return void
 */
function ChangerResoluSujet($sujet_id, $resolu_actuel)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($resolu_actuel)
	{
		//Si le sujet est résolu, on le met normal.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_resolu = 0
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
	else
	{
		//Si c'est un sujet normal, on le met en résolu.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_resolu = 1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
}

/**
 * Change le statut du message (a aidé ou non).
 * @param integer $message_id			L'id du message.
 * @param integer $help_souhaite		A-t-il aidé ou pas ?
 * @return void
 */
function ChangerHelp($message_id, $help_souhaite)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($help_souhaite)
	{
		$stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_help = 1
		WHERE message_id = :message_id");
		$stmt->bindParam(':message_id', $message_id);
		$stmt->execute();
	}
	else
	{
		$stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_help = 0
		WHERE message_id = :message_id");
		$stmt->bindParam(':message_id', $message_id);
		$stmt->execute();
	}
}

/**
 * Change le statut d'annonce (ou pas) d'un sujet.
 * @param integer $sujet_id			L'id du sujet concerné.
 * @param boolean $type_actuel		Son statut actuel.
 */
function ChangerTypeSujet($sujet_id, $type_actuel)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($type_actuel)
	{
		//Si c'est une annonce, on le met en normal.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_annonce = 0
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
	else
	{
		//Si c'est un sujet normal, on le met en annonce.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_annonce = 1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
}

function ChangerCoupCoeur($sujet_id, $type_actuel)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($type_actuel)
	{
		//S'il est en coup de coeur, on le retire
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_coup_coeur = 0
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);

		$stmt->execute();

	}
	else
	{
		//S'il n'est pas en coup de coeur, on l'y met
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_coup_coeur = 1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);

		$stmt->execute();

	}
}

/**
 * Retourne la liste des sujets en coup de coeur.
 * @author Barbatos
 * @return array
 */
function ListerSujetsCoupsCoeur()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT sujet_id, sujet_titre, sujet_auteur, sujet_coup_coeur, sujet_reponses
	FROM zcov2_forum_sujets
	WHERE sujet_coup_coeur = 1");
	$stmt->execute();
	return $stmt->fetchAll();
}

/**
 * Change le statut fermé (ou pas) d'un sujet.
 * @param integer $sujet_id			L'id du sujet concerné.
 * @param boolean $type_actuel		Son statut actuel.
 */
function ChangerStatutSujet($sujet_id, $statut_actuel)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($statut_actuel)
	{
		//Si le sujet est fermé, on l'ouvre.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_ferme = 0
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
	else
	{
		//Si le sujet est ouvert, on le ferme.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_ferme = 1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->execute();
	}
}

/**
 * Change le statut ouvert / fermé d'un sondage sur un sujet.
 * @param integer $sondage_id		L'id du sondage.
 * @param integer $statut_actuel	Le statut actuel.
 * @return void
 */
function ChangerStatutSondage($sondage_id, $statut_actuel, $forum_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($statut_actuel)
	{
		//Si le sondage est fermé, on l'ouvre.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sondages
		SET sondage_ferme = 0
		WHERE sondage_id = :sondage_id");
		$stmt->bindParam(':sondage_id', $sondage_id);
		$stmt->execute();
	}
	else
	{
		//Si le sondage est ouvert, on le ferme.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sondages
		SET sondage_ferme = 1
		WHERE sondage_id = :sondage_id");
		$stmt->bindParam(':sondage_id', $sondage_id);
		$stmt->execute();
	}
}

/**
 * Déplace un sujet de forum.
 * @param integer $id_suj			L'id du sujet à déplacer.
 * @param integer $forum_source		L'id du forum du sujet.
 * @param integer $forum_cible		L'id du forum ciblé.
 * @return void
 */
function DeplacerSujet($id_suj, $forum_source, $forum_cible)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On met à jour le sujet (on le change de forum).
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_forum_id = :forum_cible
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_id', $id_suj);
	$stmt->bindParam(':forum_cible', $forum_cible);
	$stmt->execute();
	$stmt->closeCursor();

	//On recherche le dernier message du forum source.
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_source AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':forum_source', $forum_source);
	$stmt->execute();
	$FofoSource = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if(empty($FofoSource['message_id']))
	{
		$FofoSource['message_id'] = 0;
	}

	//Maintenant qu'on a le dernier message du forum source, on update.
	$stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_source");
	$stmt->bindParam(':forum_dernier_post_id', $FofoSource['message_id']);
	$stmt->bindParam(':forum_source', $forum_source);
	$stmt->execute();
	$stmt->closeCursor();

	//On recherche le dernier message du forum cible.
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_cible AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':forum_cible', $forum_cible);
	$stmt->execute();
	$FofoCible = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if(empty($FofoCible['message_id']))
	{
		$FofoCible['message_id'] = 0;
	}

	//Maintenant qu'on a le dernier message du forum cible, on update.
	$stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_cible");
	$stmt->bindParam(':forum_dernier_post_id', $FofoCible['message_id']);
	$stmt->bindParam(':forum_cible', $forum_cible);
	$stmt->execute();
	$stmt->closeCursor();
}

/**
 * Supprime un sujet.
 * @param integer $sujet_id					L'id du sujet.
 * @param integer $forum_id					L'id de son forum.
 * @param boolean $corbeille				Est-il en corbeille ?
 * @param integer|null $sujet_sondage		L'id du sondage associé.
 * @return void
 */
function Supprimer($sujet_id, $forum_id, $corbeille, $sujet_sondage = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(is_null($sujet_sondage))
	{
		$stmt = $dbh->prepare("SELECT sujet_sondage FROM zcov2_forum_sujets WHERE sujet_id = :id");
		$stmt->bindParam(':id', $sujet_id);

		$stmt->execute();

		$sujet_sondage = $stmt->fetchColumn();
		$stmt->closeCursor();
	}

	//http://www.siteduzero.com/forum-83-168652-1553415.html#r1553415

	if(!$corbeille)
	{
		//On calcule le nombre de messages postés par membre, mais uniquement les messages postés dans le sujet à supprimer.
		$stmt = $dbh->prepare("
		SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :sujet_id
		GROUP BY message_auteur
		");
		$stmt->bindParam(':sujet_id', $sujet_id);

		$NombreMessageDesPosteursDansSujet = array();
		if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$NombreMessageDesPosteursDansSujet[] = $resultat;
			}
		}

		$stmt->closeCursor();

		//On va voir ici tout l'avantage des requêtes préparées !
		//On met à jour le nombre de messages par membres...

		$stmt = $dbh->prepare("
		UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages - :nombre_a_enlever
		WHERE utilisateur_id = :message_auteur
		");

		foreach($NombreMessageDesPosteursDansSujet as $clef => &$valeur)
		{
			$stmt->bindParam(':nombre_a_enlever', $valeur['NombreMessageDesPosteursDansSujet']);
			$stmt->bindParam(':message_auteur', $valeur['message_auteur']);

			$stmt->execute();
		}
		$stmt->closeCursor();

		//On met à jour l'index de la recherche.
		$stmt = $dbh->prepare("SELECT message_id FROM zcov2_forum_messages WHERE message_sujet_id = :id");
		$stmt->bindParam(':id', $sujet_id);
		$stmt->execute();
		$messages = $stmt->fetchAll();
	}

	//On supprime le sujet.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sujets
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_id', $sujet_id);

	$stmt->execute();

	$stmt->closeCursor();

	//On supprime les messages du sujet.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id");
	$stmt->bindParam(':message_sujet_id', $sujet_id);

	$stmt->execute();

	$stmt->closeCursor();

	//On supprime les alertes du sujet.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_alertes
	WHERE alerte_sujet_id = :message_sujet_id");
	$stmt->bindParam(':message_sujet_id', $sujet_id);

	$stmt->execute();

	$stmt->closeCursor();

################ DÉBUT sondage ################
	//On supprime le sondage du sujet.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages
	WHERE sondage_id = :sondage_id");
	$stmt->bindParam(':sondage_id', $sujet_sondage);

	$stmt->execute();

	$stmt->closeCursor();

	//On supprime les choix du sondage
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_choix
	WHERE choix_sondage_id = :choix_sondage_id");
	$stmt->bindParam(':choix_sondage_id', $sujet_sondage);

	$stmt->execute();

	$stmt->closeCursor();

	//On supprime les votes du sondage
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_votes
	WHERE vote_sondage_id = :vote_sondage_id");
	$stmt->bindParam(':vote_sondage_id', $sujet_sondage);

	$stmt->execute();

	$stmt->closeCursor();
################ FIN sondage ################

	//On supprime les enregistrements de la table lu / non-lu concernant ce sujet.
	//Ils ne dérangent pas mais ils ne servent plus à rien. Donc autant les supprimer !
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_lunonlu
	WHERE lunonlu_sujet_id = :lunonlu_sujet_id");
	$stmt->bindParam(':lunonlu_sujet_id', $sujet_id);

	$stmt->execute();

	$stmt->closeCursor();

	//On recherche le dernier message du forum.
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = :zero
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->bindValue(':zero', 0);

	$stmt->execute();
	$Fofo = $stmt->fetch(PDO::FETCH_ASSOC);

	$stmt->closeCursor();

	if(empty($Fofo['message_id']))
	{
		$Fofo['message_id'] = 0;
	}

	//Maintenant qu'on a le dernier message du forum, on update.
	$stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
	$stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->execute();
	$stmt->closeCursor();
}

function SupprimerMessage($message_id, $sujet_id, $sujet_dernier_message, $forum_id, $corbeille)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!$corbeille)
	{
		//On cherche l'auteur du message
		$stmt = $dbh->prepare("SELECT message_auteur
		FROM zcov2_forum_messages
		WHERE message_id = :message_id");
		$stmt->bindParam(':message_id', $message_id);

		$stmt->execute();
		$AuteurMessage = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt->closeCursor();

		//On décrémente le nombre de messages du membre :)
		$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages-1
		WHERE utilisateur_id = :utilisateur_id");
		$stmt->bindParam(':utilisateur_id', $AuteurMessage['message_auteur']);
		$stmt->execute();

		$stmt->closeCursor();
	}

	//On supprime le message.
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_messages
	WHERE message_id = :message_id");
	$stmt->bindParam(':message_id', $message_id);

	$stmt->execute();

	$stmt->closeCursor();

	//Il faut vérifier si dans la base de données si le message qu'on supprime était le dernier message vu par quelqu'un.
	//On récupère juste un tableau ici. Les updates se font en fin de fonction.
	$stmt = $dbh->prepare("SELECT lunonlu_utilisateur_id FROM zcov2_forum_lunonlu
	WHERE lunonlu_message_id = :lunonlu_message_id");
	$stmt->bindParam(':lunonlu_message_id', $message_id);

	$stmt->execute();
	if ($MettreAjourDernierMessageVu[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$MettreAjourDernierMessageVu[] = $resultat;
		}
	}

	$stmt->closeCursor();

	if($message_id == $sujet_dernier_message)
	{
		//On récupère le dernier message du sujet (il a changé vu qu'on vient de supprimer un message...)
		$stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :message_sujet_id
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
		$stmt->bindParam(':message_sujet_id', $sujet_id);

		$stmt->execute();
		$DernierMessSujet = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt->closeCursor();

		//On update la table du sujet, pour indiquer le dernier message du sujet (qu'on vient de récupérer au-dessus) et pour décrémenter le nombre de réponses.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_dernier_message = :sujet_dernier_message,
		sujet_reponses = sujet_reponses-1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_dernier_message', $DernierMessSujet['message_id']);
		$stmt->bindParam(':sujet_id', $sujet_id);

		$stmt->execute();

		$stmt->closeCursor();

		//On recherche le dernier message du forum.
		$stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = :zero
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
		$stmt->bindParam(':forum_id', $forum_id);
		$stmt->bindValue(':zero', 0);

		$stmt->execute();
		$Fofo = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt->closeCursor();

		if(empty($Fofo['message_id']))
		{
			$Fofo['message_id'] = 0;
		}

		//Maintenant qu'on a le dernier message du forum, on update.
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
		$stmt->bindParam(':forum_id', $forum_id);

		$stmt->execute();

		$stmt->closeCursor();
	}
	else
	{
		//On update la table du sujet, pour décrémenter le nombre de réponses.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_reponses = sujet_reponses-1
		WHERE sujet_id = :sujet_id");
		$stmt->bindParam(':sujet_id', $sujet_id);

		$stmt->execute();

		$stmt->closeCursor();
	}

	if(!empty($MettreAjourDernierMessageVu[0]['lunonlu_utilisateur_id']) AND $message_id != $sujet_dernier_message)
	{
		//On récupère le dernier message du sujet (il a changé vu qu'on vient de supprimer un message...)
		$stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :message_sujet_id
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
		$stmt->bindParam(':message_sujet_id', $sujet_id);

		$stmt->execute();
		$DernierMessSujet = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt->closeCursor();
	}

	if(!empty($MettreAjourDernierMessageVu[0]['lunonlu_utilisateur_id']))
	{
		//Voilà, c'est ici qu'on update le système lu /nonlu (voir deuxième requête de cette fonction => tout en haut).
		$LeBindParam = '';
		foreach($MettreAjourDernierMessageVu as $clef => $valeur)
		{
			$LeBindParam .= $MettreAjourDernierMessageVu[$clef]['lunonlu_utilisateur_id'].',';
		}
		$LeBindParam = substr($LeBindParam, 0, -1);

		$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
		SET lunonlu_message_id = :lunonlu_message_id
		WHERE lunonlu_utilisateur_id IN(:lunonlu_utilisateur_id) AND lunonlu_sujet_id = :lunonlu_sujet_id");
		$stmt->bindParam(':lunonlu_message_id', $DernierMessSujet['message_id']);
		$stmt->bindParam(':lunonlu_utilisateur_id', $LeBindParam);
		$stmt->bindParam(':lunonlu_sujet_id', $sujet_id);

		$stmt->execute();

		$stmt->closeCursor();
	}
}

/**
 * Jette un sujet à la corbeille.
 * @param integer $sujet_id			L'id du sujet.
 * @param integer $forum_id			L'id du forum dans lequel est le sujet.
 * @return void
 */
function Corbeille($sujet_id, $forum_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//http://www.siteduzero.com/forum-83-168652-1553415.html#r1553415

	//On calcule le nombre de messages postés par membre, mais uniquement les
	//messages postés dans le sujet à mettre en corbeille.
	$stmt = $dbh->prepare("
	SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :sujet_id
	GROUP BY message_auteur
	");
	$stmt->bindParam(':sujet_id', $sujet_id);

	$NombreMessageDesPosteursDansSujet = array();
	if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$NombreMessageDesPosteursDansSujet[] = $resultat;
		}
	}
	$stmt->closeCursor();

	//On va voir ici tout l'avantage des requêtes préparées !
	//On met à jour le nombre de messages par membres...
	$stmt = $dbh->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_forum_messages = utilisateur_forum_messages - :nombre_a_enlever
	WHERE utilisateur_id = :message_auteur
	");

	foreach($NombreMessageDesPosteursDansSujet as $clef => &$valeur)
	{
		$stmt->bindParam(':nombre_a_enlever', $valeur['NombreMessageDesPosteursDansSujet']);
		$stmt->bindParam(':message_auteur', $valeur['message_auteur']);
		$stmt->execute();
	}
	$stmt->closeCursor();

	//On met le sujet en corbeille.
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_corbeille = 1
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_id', $sujet_id);
	$stmt->execute();
	$stmt->closeCursor();


	//On recherche le dernier message du forum.
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->execute();
	$Fofo = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if(empty($Fofo['message_id']))
	{
		$Fofo['message_id'] = 0;
	}

	//Maintenant qu'on a le dernier message du forum, on update.
	$stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
	$stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->execute();
	$stmt->closeCursor();

	//On met à jour l'index de la recherche.
	$stmt = $dbh->prepare("SELECT message_id FROM zcov2_forum_messages WHERE message_sujet_id = :id");
	$stmt->bindParam(':id', $sujet_id);
	$stmt->execute();
	$messages = $stmt->fetchAll();
}

/**
 * Sort un sujet de la corbeille.
 * @param integer $sujet_id			L'id du sujet.
 * @param integer $forum_id			L'id du forum dans lequel est le sujet.
 * @return void
 */
function Restaurer($sujet_id, $forum_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//http://www.siteduzero.com/forum-83-168652-1553415.html#r1553415

	//On calcule le nombre de messages postés par membre, mais uniquement les
	//messages postés dans le sujet à restaurer.
	$stmt = $dbh->prepare("
	SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :sujet_id
	GROUP BY message_auteur
	");
	$stmt->bindParam(':sujet_id', $sujet_id);

	$NombreMessageDesPosteursDansSujet = array();
	if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$NombreMessageDesPosteursDansSujet[] = $resultat;
		}
	}
	$stmt->closeCursor();

	//On va voir ici tout l'avantage des requêtes préparées !
	//On met à jour le nombre de messages par membres...
	$stmt = $dbh->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_forum_messages = utilisateur_forum_messages + :nombre_a_ajouter
	WHERE utilisateur_id = :message_auteur
	");

	foreach($NombreMessageDesPosteursDansSujet as $clef => &$valeur)
	{
		$stmt->bindParam(':nombre_a_ajouter', $valeur['NombreMessageDesPosteursDansSujet']);
		$stmt->bindParam(':message_auteur', $valeur['message_auteur']);
		$stmt->execute();
	}
	$stmt->closeCursor();

	//On restaure le sujet.
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_corbeille = 0
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_id', $sujet_id);
	$stmt->execute();
	$stmt->closeCursor();


	//On recherche le dernier message du forum.
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->execute();
	$Fofo = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if(empty($Fofo['message_id']))
	{
		$Fofo['message_id'] = 0;
	}

	//Maintenant qu'on a le dernier message du forum, on update.
	$stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
	$stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
	$stmt->bindParam(':forum_id', $forum_id);
	$stmt->execute();
	$stmt->closeCursor();

	//On met à jour l'index de la recherche.
	$stmt = $dbh->prepare("SELECT message_id, message_date, message_texte,
		utilisateur_pseudo, sujet_titre, sujet_id, sujet_forum_id, sujet_ferme, sujet_resolu
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id
		LEFT JOIN zcov2_utilisateurs ON message_auteur = utilisateur_id
		WHERE message_sujet_id = :id");
	$stmt->bindParam(':id', $sujet_id);
	$stmt->execute();
	$messages = $stmt->fetchAll();
}

function DiviserSujet($infos, $corbeille)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On supprime tous les enregistrements de la tables des lu / non-lus
	$stmt = $dbh->prepare("DELETE FROM zcov2_forum_lunonlu WHERE lunonlu_sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_id', $infos['sujet_id']);

	$stmt->execute();

	$stmt->closeCursor();


	//On crée le nouveau sujet
	//--- On récupère ldes infos sur les messages à déplacer
	$in = array();
	foreach($_POST['msg'] as $cle => $valeur)
		$in[] = $cle;

	$stmt = $dbh->prepare("SELECT message_id, message_auteur
	FROM zcov2_forum_messages
	WHERE message_id IN(".implode(', ', $in).")
	ORDER BY message_date ASC");

	$stmt->execute();

	$messages = $stmt->fetchAll();
	$stmt->closeCursor();

	//--- Insertion en BDD
	$stmt = $dbh->prepare("INSERT INTO zcov2_forum_sujets (sujet_forum_id, sujet_titre, sujet_sous_titre, sujet_auteur, sujet_date, sujet_premier_message, sujet_dernier_message, sujet_annonce, sujet_ferme, sujet_resolu, sujet_corbeille, sujet_reponses)
	VALUES (:sujet_forum_id, :sujet_titre, :sujet_sous_titre, :sujet_auteur, NOW(), :premier_message, :dernier_message, 0, 0, 0, 0, :reponses)");
	$stmt->bindParam(':sujet_forum_id', $_POST['forum']);
	$stmt->bindParam(':sujet_titre', $_POST['titre']);
	$stmt->bindParam(':sujet_sous_titre', $_POST['sous_titre']);
	$stmt->bindParam(':sujet_auteur', $messages[0]['message_auteur']);
	$stmt->bindParam(':premier_message', $messages[0]['message_id']);
	$stmt->bindParam(':dernier_message', $messages[(count($messages) - 1)]['message_id']);
	$stmt->bindValue(':reponses', (count($messages) -1));

	$stmt->execute();


	//--- On récupère l'id de l'enregistrement qui vient d'être créé (l'id du nouveau sujet).
	$nouveau_sujet_id = $dbh->lastInsertId();
	$stmt->closeCursor();

	//-- On déplace les posts
	$stmt = $dbh->prepare("
	UPDATE zcov2_forum_messages
	SET message_sujet_id = :id_sujet
	WHERE message_id IN(".implode(', ', $in).")");
	$stmt->bindParam(':id_sujet', $nouveau_sujet_id);

	$stmt->execute();

	$stmt->closeCursor();

	//--- Puis on ajoute des enregistrements de la table lu / nonlu
	foreach($messages as $m)
	{
		$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
		VALUES (:user_id, :sujet_id, :message_id, '1')
		ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
		$stmt->bindParam(':user_id', $m['message_auteur']);
		$stmt->bindParam(':sujet_id', $nouveau_sujet_id);
		$stmt->bindParam(':message_id', $m['message_id']);

		$stmt->execute();

		$stmt->closeCursor();
	}

	//--- Si on n'est pas dans la corbeille, il faut penser à mettre à jour le dernier message posté du forum
	if(!$corbeille)
	{
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $messages[(count($messages) - 1)]['message_id']);
		$stmt->bindParam(':forum_id', $_POST['forum']);

		$stmt->execute();

		$stmt->closeCursor();
	}


	//Enfin on s'occupe de l'ancien sujet
	//--- On récupère le premier message du sujet
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date ASC, message_id ASC
	LIMIT 0, 1");
	$stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

	$stmt->execute();
	$PremierMessSujet = $stmt->fetchColumn();

	$stmt->closeCursor();

	//--- On récupère le dernier message du sujet
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

	$stmt->execute();
	$DernierMessSujet = $stmt->fetchColumn();

	$stmt->closeCursor();

	//--- On update la table du sujet, pour indiquer le premier et le dernier message du sujet, et pour décrémenter le nombre de réponses
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :premier_message,  sujet_dernier_message = :dernier_message, sujet_reponses = sujet_reponses - :nb
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':premier_message', $PremierMessSujet);
	$stmt->bindParam(':dernier_message', $DernierMessSujet);
	$stmt->bindValue(':nb', count($messages));
	$stmt->bindParam(':sujet_id', $infos['sujet_id']);

	$stmt->execute();

	$stmt->closeCursor();

	//--- Puis on met à jour les enregistrements de la table lu / nonlu
	$ListerMessages = ListerMessages($infos['sujet_id'],  0, $infos['nombre_de_messages']);
	foreach($ListerMessages as $m)
	{
		//Si le message ne doit pas être supprimé
		if(!in_array($m['message_id'], $in))
		{
			$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
			VALUES (:user_id, :sujet_id, :message_id, '1')
			ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
			$stmt->bindParam(':user_id', $m['message_auteur']);
			$stmt->bindParam(':sujet_id', $infos['sujet_id']);
			$stmt->bindParam(':message_id', $m['message_id']);

			$stmt->execute();

			$stmt->closeCursor();
		}
	}

	if(!$corbeille)
	{
		//--- On recherche le dernier message du forum.
		$stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
		$stmt->bindParam(':forum_id', $infos['forum_id']);

		$stmt->execute();
		$Fofo = $stmt->fetchColumn();

		$stmt->closeCursor();

		//--- Maintenant qu'on a le dernier message du forum, on update.
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $Fofo);
		$stmt->bindParam(':forum_id', $infos['forum_id']);

		$stmt->execute();

		$stmt->closeCursor();
	}
}

function FusionnerSujets($infos, $corbeille)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$in = array();

	foreach($_POST['sujet'] as $cle => $valeur)
	{
		$in[] = $cle;
	}

	//On récupère des infos sur les sujets
	$stmt = $dbh->prepare("SELECT sujet_id, sujet_reponses, sujet_forum_id, sujet_corbeille, sujet_sondage
	FROM zcov2_forum_sujets
	WHERE sujet_id IN(".implode(', ', $in).")");
	$stmt->execute();
	$sujets = $stmt->fetchAll();

	$stmt->closeCursor();

	//On change les messages de sujet
	$stmt = $dbh->prepare("
	UPDATE zcov2_forum_messages
	SET message_sujet_id = :new
	WHERE message_sujet_id IN(".implode(', ', $in).")");
	$stmt->bindParam(':new', $infos['sujet_id']);

	$stmt->execute();

	$stmt->closeCursor();

	//On calcule le nombre de messages à ajouter
	$nb_messages = 0;
	foreach($sujets as $s)
	{
		if($s['sujet_id'] != $infos['sujet_id'])
			$nb_messages += ($s['sujet_reponses'] + 1);
	}

	//Mise à jour du sujet parent
	//--- On récupère le premier message du sujet
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date ASC, message_id ASC
	LIMIT 0, 1");
	$stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

	$stmt->execute();
	$PremierMessSujet = $stmt->fetchColumn();

	$stmt->closeCursor();

	//--- On récupère le dernier message du sujet
	$stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
	$stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

	$stmt->execute();
	$DernierMessSujet = $stmt->fetchColumn();

	$stmt->closeCursor();

	//--- On update la table du sujet, pour indiquer le premier et le dernier message du sujet, et pour augmenter le nombre de réponses
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :premier_message,  sujet_dernier_message = :dernier_message, sujet_reponses = sujet_reponses + :nb
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':premier_message', $PremierMessSujet);
	$stmt->bindParam(':dernier_message', $DernierMessSujet);
	$stmt->bindParam(':nb', $nb_messages);
	$stmt->bindParam(':sujet_id', $infos['sujet_id']);

	$stmt->execute();

	$stmt->closeCursor();

	//On supprime les anciens sujets (les messages ont déjà été déplacés, ils ne seront pas supprimés)
	foreach($sujets as $s)
	{
		if($s['sujet_id'] != $infos['sujet_id'])
			Supprimer($s['sujet_id'], $s['sujet_forum_id'], $s['sujet_corbeille'], $s['sujet_sondage']);
	}

	//Mise à jour pour le forum
	if(!$corbeille)
	{
		//--- On recherche le dernier message du forum.
		$stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
		$stmt->bindParam(':forum_id', $infos['forum_id']);

		$stmt->execute();
		$Fofo = $stmt->fetchColumn();

		$stmt->closeCursor();

		//--- Maintenant qu'on a le dernier message du forum, on update.
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $Fofo);
		$stmt->bindParam(':forum_id', $infos['forum_id']);

		$stmt->execute();

		$stmt->closeCursor();
	}

	//Mise à jour pour les lus / non-lus
	$stmt = $dbh->prepare("SELECT message_id, message_auteur
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :id");
	$stmt->bindParam(':id', $infos['sujet_id']);

	$stmt->execute();

	$messages = $stmt->fetchAll();
	$stmt->closeCursor();

	foreach($messages as $m)
	{
		$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
		VALUES (:user_id, :sujet_id, :message_id, '1')
		ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
		$stmt->bindParam(':user_id', $m['message_auteur']);
		$stmt->bindParam(':sujet_id', $infos['sujet_id']);
		$stmt->bindParam(':message_id', $m['message_id']);

		$stmt->execute();

		$stmt->closeCursor();
	}
}
