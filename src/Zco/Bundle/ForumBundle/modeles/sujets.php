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
 * Modèle concernant tout ce qui est listage du contenu d'un sujet (donc ses messages quoi),
 * et d'autres fonction nécessaires qui sont utilisées dans la page voir_sujet.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @begin 30/06/2007
 * @last 01/01/09
 */

function InfosSujet($lesujet)
{
	if(!verifier('connecte'))
	{
		$userid = 0;
	}
	else
	{
		$userid = $_SESSION['id'];
	}
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT sujet_id, sujet_titre, sujet_sous_titre, sujet_premier_message, sujet_dernier_message, sujet_auteur, sujet_ferme, sujet_annonce, sujet_sondage, sujet_resolu, sujet_corbeille, sujet_coup_coeur, " .
			"sondage_question, sondage_ferme, sujet_forum_id, utilisateur_sexe," .
			"COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
			"COUNT(*) AS nombre_de_messages, cat_reglement, " .
			"lunonlu_utilisateur_id, lunonlu_message_id, lunonlu_favori, vote_membre_id, " .
			"Na.message_date AS dernier_message_date, Na.message_auteur AS dernier_message_auteur " .
			"FROM zcov2_forum_sujets " .
			"LEFT JOIN zcov2_forum_messages ON zcov2_forum_sujets.sujet_id = zcov2_forum_messages.message_sujet_id " .
			"LEFT JOIN zcov2_forum_messages Na ON zcov2_forum_sujets.sujet_dernier_message = Na.message_id " .
			"LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
			"LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_sujets.sujet_auteur = Ma.utilisateur_id " .
			"LEFT JOIN zcov2_forum_sondages ON zcov2_forum_sujets.sujet_sondage = zcov2_forum_sondages.sondage_id " .
			"LEFT JOIN zcov2_forum_sondages_votes ON ".$userid." = zcov2_forum_sondages_votes.vote_membre_id AND zcov2_forum_sujets.sujet_sondage = zcov2_forum_sondages_votes.vote_sondage_id " .
			"LEFT JOIN zcov2_forum_lunonlu ON zcov2_forum_sujets.sujet_id = zcov2_forum_lunonlu.lunonlu_sujet_id AND ".$userid." = zcov2_forum_lunonlu.lunonlu_utilisateur_id " .
			"WHERE sujet_id = :s " .
			"GROUP BY sujet_id");
	$stmt->bindParam(':s', $lesujet);

	$stmt->execute();

	$resultat = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if((!empty($resultat['sujet_id']) AND
		verifier('voir_sujets', $resultat['sujet_forum_id']) AND (!$resultat['sujet_corbeille'] OR verifier('corbeille_sujets', $resultat['sujet_forum_id']))))
	{
		return $resultat;
	}
	else
	{
		return false;
	}
}

function ListerMessages($id, $PremierMess, $MessaAfficher)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT DISTINCT message_id, message_auteur, Ma.utilisateur_id_groupe AS auteur_groupe, Ma.utilisateur_sexe,
	message_texte, message_date, message_ip, message_help, groupe_class, groupe_nom, groupe_logo, groupe_logo_feminin, 
	Ma.utilisateur_nb_sanctions, Ma.utilisateur_forum_messages, Ma.utilisateur_pourcentage, Ma.utilisateur_site_web,
	Ma.utilisateur_titre, message_date, message_sujet_id, message_edite_auteur, message_edite_date,
	sujet_date, Ma.utilisateur_citation, Ma.utilisateur_absent, Ma.utilisateur_fin_absence,
	COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS auteur_message_pseudo, Ma.utilisateur_avatar AS auteur_avatar,
	COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS auteur_edition_pseudo,
	Mb.utilisateur_id AS auteur_edition_id,
	Ma.utilisateur_signature AS auteur_message_signature, sujet_auteur, sujet_premier_message, sujet_dernier_message, 
	sujet_sondage, sujet_annonce, sujet_ferme
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_messages.message_auteur = Ma.utilisateur_id
	LEFT JOIN zcov2_utilisateurs Mb ON zcov2_forum_messages.message_edite_auteur = Mb.utilisateur_id
	LEFT JOIN zcov2_groupes ON Ma.utilisateur_id_groupe=groupe_id
	WHERE sujet_id = :s
	ORDER BY message_date ASC
	LIMIT ".$MessaAfficher." OFFSET ".$PremierMess);

	$stmt->bindParam(':s', $id);
	$stmt->execute();


	return $stmt->fetchAll();
}

function TrouverLaPageDeCeMessage($id, $Message)
{
	//Dès qu'il y a un paramètre "m" dans l'URL, cette fonction est appelée pour trouver sur quelle page du sujet se trouve le message concerné.
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :s
	ORDER BY message_date ASC");
	$stmt->bindParam(':s', $id);

	if ($stmt->execute())
	{
 		while($resultat0 = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$resultat[] = $resultat0;
		}
	}


	//On calcule le nombre total de messages.
	$totalDesMessages = 0;
	foreach($resultat as $clef => $valeur)
	{
		$totalDesMessages++;
	}

	//On calcule la position du message
	$arreter_boucle = false;
	$i = 1;
	$PositionDansLaPage = 1;
	$nbMessagesParPage = 20;
	foreach($resultat as $clef => $valeur)
	{
		if(!$arreter_boucle)
		{
			if($valeur["message_id"] != $Message)
			{
				if($PositionDansLaPage == $nbMessagesParPage)
				{
					$PositionDansLaPage = 0;
				}
				$i++;
				$PositionDansLaPage++;
			}
			else
			{
				$arreter_boucle = true;
			}
		}
	}
	$nombreDePages = ceil($totalDesMessages / $nbMessagesParPage);
	$PageCible = ceil($i/$nbMessagesParPage);
	if($PositionDansLaPage == $nbMessagesParPage AND $PageCible < $nombreDePages)
	{
		$PageCible++;
	}
	return $PageCible;
}

function RendreLeSujetLu($sujet_id, $nombreDePages, $dernier_message, $ListerMessages, $InfosLuNonlu)
{
	if (!empty($InfosLuNonlu['lunonlu_utilisateur_id']))
	{
		$dejavu = true;
	}
	else
	{
		$dejavu = false;
	}

	$dbh = Doctrine_Manager::connection()->getDbh();

	//Si on est sur la page la plus récente, on considère que le sujet entier est lu (jusqu'à son dernier message
	if($_GET['p'] == $nombreDePages)
	{
		if(!$dejavu)
		{
			//Si c'est la première fois qu'on visite le sujet, on insère un nouvel enregistrement
			$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
			VALUES (:user_id, :sujet_id, :message_id, '0')");
			$stmt->bindParam(':user_id', $_SESSION['id']);
			$stmt->bindParam(':sujet_id', $sujet_id);
			$stmt->bindParam(':message_id', $dernier_message);

			$stmt->execute();

			$stmt->closeCursor();
		}
		else
		{
			if($InfosLuNonlu['lunonlu_message_id'] != $dernier_message)
			{
				//Sinon, on met simplement à jour si besoin (que si les deux valeurs diffèrent...).
				$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
				SET lunonlu_message_id = :message_id
				WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
				$stmt->bindParam(':user_id', $_SESSION['id']);
				$stmt->bindParam(':sujet_id', $sujet_id);
				$stmt->bindParam(':message_id', $dernier_message);

				$stmt->execute();

				$stmt->closeCursor();
			}
		}
	}
	else
	{
		//Si on est sur une autre page que la plus récente, on considère que le sujet est lu jusqu'au dernier message s'affichant dans la page courante.
		//Donc on doit trouver le dernier message de la page courante...
		$i = 1;
		foreach($ListerMessages as $clef => $valeur)
		{
			if($i == 20)
			{
				$MessageLePlusRecentDansLaPage = $valeur['message_id'];
			}
			$i++;
		}
		if(!isset($MessageLePlusRecentDansLaPage))
			$MessageLePlusRecentDansLaPage = $valeur['message_id'];

		//Ok, maintenant on a le dernier message de la page courante :)
		//On vérifie avant bien sûr que la mise à jour est nécessaire. Sinon on ne la fait pas :) La condition suivante nous économise quand-même une requête UPDATE quand elle est inutile ;)
		if($InfosLuNonlu['lunonlu_message_id'] < $MessageLePlusRecentDansLaPage)
		{
			if(!$dejavu)
			{
				//Si c'est la première fois qu'on visite le sujet, on insère un nouvel enregistrement
				$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
				VALUES (:user_id, :sujet_id, :message_id, '0')");
				$stmt->bindParam(':user_id', $_SESSION['id']);
				$stmt->bindParam(':sujet_id', $sujet_id);
				$stmt->bindParam(':message_id', $MessageLePlusRecentDansLaPage);

				$stmt->execute();

				$stmt->closeCursor();
			}
			else
			{

				//Sinon, on met simplement à jour si besoin (que si les deux valeurs diffèrent...).
				$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
				SET lunonlu_message_id = :message_id
				WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
				$stmt->bindParam(':user_id', $_SESSION['id']);
				$stmt->bindParam(':sujet_id', $sujet_id);
				$stmt->bindParam(':message_id', $MessageLePlusRecentDansLaPage);

				$stmt->execute();

				$stmt->closeCursor();
			}
		}
	}
	return true;
}

function RevueSujet($sujet_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT message_id, message_auteur, Ma.utilisateur_id_groupe AS auteur_groupe, message_texte, message_date, groupe_class, groupe_nom, groupe_logo,
	message_date, sujet_date, message_edite_date, message_sujet_id, message_edite_auteur,
	COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS auteur_message_pseudo, Ma.utilisateur_avatar AS auteur_avatar,
	COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS auteur_edition_pseudo, Mb.utilisateur_id AS auteur_edition_id,
	Ma.utilisateur_signature AS auteur_message_signature, Ma.utilisateur_citation, Ma.utilisateur_titre, sujet_auteur, sujet_premier_message, sujet_dernier_message, sujet_sondage, sujet_annonce, sujet_ferme

	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_messages.message_auteur = Ma.utilisateur_id
	LEFT JOIN zcov2_utilisateurs Mb ON zcov2_forum_messages.message_edite_auteur = Mb.utilisateur_id
	LEFT JOIN zcov2_groupes ON Ma.utilisateur_id_groupe=groupe_id
	WHERE sujet_id = :s
	ORDER BY message_date DESC
	LIMIT 15");

	$stmt->bindParam(':s', $sujet_id);

	$retour = array();
	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		return $retour;
	}
	else
	{
		return false;
	}
}

function EnregistrerNouveauSujet($id, $annonce, $ferme, $resolu, $corbeille)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Création du sujet.
	$stmt = $dbh->prepare("INSERT INTO zcov2_forum_sujets (sujet_id, sujet_forum_id, sujet_titre, sujet_sous_titre, sujet_auteur, sujet_date, sujet_premier_message, sujet_dernier_message, sujet_sondage, sujet_annonce, sujet_ferme, sujet_resolu, sujet_corbeille)
	VALUES ('', :sujet_forum_id, :sujet_titre, :sujet_sous_titre, :sujet_auteur, NOW(), '', '', 0, :sujet_annonce, :sujet_ferme, :sujet_resolu, :sujet_corbeille)");
	$stmt->bindParam(':sujet_forum_id', $id);
	$stmt->bindParam(':sujet_titre', $_POST['titre']);
	$stmt->bindParam(':sujet_sous_titre', $_POST['sous_titre']);
	$stmt->bindParam(':sujet_auteur', $_SESSION['id']);
	$stmt->bindParam(':sujet_annonce', $annonce);
	$stmt->bindParam(':sujet_ferme', $ferme);
	$stmt->bindParam(':sujet_resolu', $resolu);
	$stmt->bindParam(':sujet_corbeille', $corbeille);
	$stmt->execute();
	$nouveau_sujet_id = $dbh->lastInsertId();
	$stmt->closeCursor();

	// Création du premier message.
	$stmt = $dbh->prepare("INSERT INTO zcov2_forum_messages (message_id, message_auteur, message_texte, message_date, message_sujet_id, message_edite_auteur, message_edite_date, message_ip)
	VALUES ('', :message_auteur, :message_texte, NOW(), :message_sujet_id, 0, '', :ip)");
	$stmt->bindParam(':message_auteur', $_SESSION['id']);
	$stmt->bindParam(':message_texte', $_POST['texte']);
	$stmt->bindParam(':message_sujet_id', $nouveau_sujet_id);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp()));
	$stmt->execute();
	$nouveau_message_id = $dbh->lastInsertId();
	$stmt->closeCursor();

	// Grâce au numéro du post récupéré, on peut updater la table des sujets pour indiquer que ce post est le premier et le dernier du sujet.
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :sujet_premier_message, sujet_dernier_message = :sujet_dernier_message
	WHERE sujet_id = :nouveau_sujet_id");
	$stmt->bindParam(':sujet_premier_message', $nouveau_message_id);
	$stmt->bindParam(':sujet_dernier_message', $nouveau_message_id);
	$stmt->bindParam(':nouveau_sujet_id', $nouveau_sujet_id);
	$stmt->execute();
	$stmt->closeCursor();

	if(!$corbeille)
	{
		//Enfin, on met à jour la table forums : on met à jour le dernier message posté du forum.
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $nouveau_message_id);
		$stmt->bindParam(':forum_id', $_GET['id']);
		$stmt->execute();

		$stmt->closeCursor();
	}

	//Puis on crée l'enregistrement pour la table lu / nonlu
	$stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
	VALUES (:user_id, :sujet_id, :message_id, '1')");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':sujet_id', $nouveau_sujet_id);
	$stmt->bindParam(':message_id', $nouveau_message_id);

	$stmt->execute();

	$stmt->closeCursor();

	if(!$corbeille)
	{
		//Enfin, on incrémente le nombre de messages du membre :)
		$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages+1
		WHERE utilisateur_id = :utilisateur_id");
		$stmt->bindParam(':utilisateur_id', $_SESSION['id']);
		$stmt->execute();


		$stmt->closeCursor();
	}
	return $nouveau_sujet_id;
}

function ChangerFavori($sujet_id, $etat)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($etat == 1)
	{
		//Si le sujet est déjà en favori, c'est qu'on veut l'enlever des favoris.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
		SET lunonlu_favori = 0
		WHERE lunonlu_sujet_id = :sujet_id AND lunonlu_utilisateur_id = :utilisateur_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->bindParam(':utilisateur_id', $_SESSION['id']);
		$stmt->execute();
	}
	else
	{
		//Sinon, on le met en favori.
		$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
		SET lunonlu_favori = 1
		WHERE lunonlu_sujet_id = :sujet_id AND lunonlu_utilisateur_id = :utilisateur_id");
		$stmt->bindParam(':sujet_id', $sujet_id);
		$stmt->bindParam(':utilisateur_id', $_SESSION['id']);
		$stmt->execute();
	}
}

/**
 * Vérifie si un sujet est dans un forum archivé ou non.
 *
 * @param 		$id		ID du sujet.
 * @return		bool	
 */
function sujetIsArchive($id)
{
	$InfosSujet = InfosSujet($id);
	$InfosForum = InfosCategorie($InfosSujet['sujet_forum_id']);
	
	return ( $InfosForum['cat_archive'] == 1 ) ? (true) : (false);
}

