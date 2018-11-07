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
 * Modèle concernant tout ce qui est listage du contenu d'un forum (donc ses sujets quoi).
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 * @begin 28/06/07
 * @last 01/01/09
 */

function CompterSujets($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Corbeille
	if(!empty($_GET['trash']) AND verifier('corbeille_sujets', $_GET['id']))
		$trash = 1;
	else
		$trash = 0;

	// S'il n'y a pas d'id, c'est que l'on ne veut pas voir un forum en particulier mais tous les sujets de tous les forums
	if(empty($id))
	{
		$add = "";

		if(isset($_GET['closed']))
		{
			$add .= ' AND sujet_ferme = '.($_GET['closed'] ? 1 : 0);
		}
		if(isset($_GET['solved']))
		{
			$add .= ' AND sujet_resolu = '.($_GET['solved'] ? 1 : 0);
		}
		if(isset($_GET['favori']))
		{
			$add .= ' AND lunonlu_favori = '.($_GET['favori'] ? 1 : 0);
		}
		if(isset($_GET['coeur']))
		{
			$add .= ' AND sujet_coup_coeur = '.($_GET['coeur'] ? 1 : 0);
		}
		if(isset($_GET['epingle']))
		{
			$add .= ' AND sujet_annonce = '.($_GET['epingle'] ? 1 : 0);
		}
	}
	else
	{
		$add = "sujet_forum_id = ".$id." AND ";
	}

	$groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
	array_unshift($groupes, $_SESSION['groupe']);
	$groupes = implode(',', $groupes);

	$stmt = $dbh->prepare("SELECT COUNT(DISTINCT sujet_id) AS nombre_sujets " .
		"FROM zcov2_forum_sujets " .
		'LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = sujet_forum_id '.
		"AND gd_id_groupe IN ($groupes) ".
		'LEFT JOIN zcov2_droits ON gd_id_droit = droit_id '.
		"WHERE sujet_forum_id = :f AND sujet_corbeille = :trash ".
		"AND droit_nom = 'voir_sujets'"
	);
	$stmt->bindParam(':f', $id);
	$stmt->bindParam(':trash', $trash);

	$stmt->execute();

	return $stmt->fetchColumn();
}

function ListerSujets($PremierMess, $MessaAfficher, $forumID = null)
{
	// Savoir si on regarde un forum en particulier ou le forum globalement
	$forums = $forumID ? 'sujet_forum_id = '.(int)$forumID.' AND' : '';

	// Ajouts au WHERE par des flags envoyées par $_GET
	$add = '';
	if(isset($_GET['closed']))
	{
		$add .= ' AND sujet_ferme = '.(int)($_GET['closed'] ? 1 : 0);
	}
	if(isset($_GET['solved']))
	{
		$add .= ' AND sujet_resolu = '.($_GET['solved'] ? 1 : 0);
	}
	if(isset($_GET['favori']))
	{
		$add .= ' AND lunonlu_favori = '.($_GET['favori'] ? 1 : 0);
	}
	if(isset($_GET['coeur']))
	{
		$add .= ' AND sujet_coup_coeur = '.($_GET['coeur'] ? 1 : 0);
	}
	if(isset($_GET['epingle']))
	{
		$add .= ' AND sujet_annonce = '.($_GET['epingle'] ? 1 : 0);
	}

	/* Fin des ajouts */

	// Corbeille
	if(!empty($_GET['trash']) AND verifier('corbeille_sujets', $_GET['id']))
	{
		$trash = 1;
	}
	else
	{
		$trash = 0;
	}

	if(!verifier('connecte'))
	{
		$lunonlu_user = 0;
	}
	else
	{
		$lunonlu_user = $_SESSION['id'];
	}
	$dbh = Doctrine_Manager::connection()->getDbh();

	$groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
	array_unshift($groupes, $_SESSION['groupe']);
	$groupes = implode(',', $groupes);
	$stmt = $dbh->prepare('SELECT sujet_id, sujet_titre, sujet_sous_titre, '.
			'sujet_coup_coeur, sujet_reponses, sujet_auteur, sujet_forum_id, '.
			'Ma.utilisateur_id_groupe AS sujet_auteur_groupe, '.
			'Ma.utilisateur_id AS sujet_auteur_pseudo_existe, '.
			'Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, '.
			"COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, ".
			"COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, ".
			'message_auteur AS sujet_dernier_message_auteur_id, sujet_date, '.
			'message_date, UNIX_TIMESTAMP(message_date) AS message_timestamp, '.
			'sujet_dernier_message, sujet_sondage, sujet_annonce, '.
			'sujet_ferme, sujet_resolu, message_id, '.
			'lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, '.
			'lunonlu_participe, lunonlu_favori, '.
			'FROM zcov2_forum_sujets '.
			'LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id '.
			'LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id '.
			'LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id '.
			'LEFT JOIN zcov2_forum_lunonlu ON sujet_id = lunonlu_sujet_id '.
			'AND '.$lunonlu_user.' = lunonlu_utilisateur_id '.
			'LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = sujet_forum_id '.
			"AND gd_id_groupe IN($groupes) ".
			'LEFT JOIN zcov2_droits ON gd_id_droit = droit_id '.
			'WHERE '.$forums.' sujet_corbeille = :trash'.$add.' '.
				"AND droit_nom = 'voir_sujets' ".
				'AND gd_valeur = 1 '.
			'GROUP BY sujet_id '.
			'ORDER BY sujet_annonce DESC, message_date DESC '.
			'LIMIT '.$PremierMess.' , '.$MessaAfficher);
	$stmt->bindParam(':trash', $trash);
	$stmt->execute();
	$sujets = $stmt->fetchAll();

	$tags = array();
	return array($sujets, $tags);
}

// Cette fonction retourne l'image du système lu/non lu.
function LuNonluForum($lu)
{
	$dejalu = $lu['derniere_lecture_globale'] > $lu['date_dernier_message'];
	$dejavu = !empty($lu['lunonlu_utilisateur_id']);

	if($dejavu || $dejalu)
	{
		// Si on a déjà lu au moins une fois ce sujet
		if(!$lu['lunonlu_participe'])
		{
			// Si on n'a pas encore posté dans ce sujet
			if($dejalu || $lu['lunonlu_message_id'] == $lu['sujet_dernier_message'])
			{
				// Si il n'y a pas de nouveau message depuis la dernière visite du membre
				$retour = array(
					'image' => 'pas_nouveau_message.png',
					'title' => 'Pas de nouvelles réponses, jamais participé',
					'fleche' => '0'
				);
			}
			else
			{
				// Si il y a un ou des nouveaux messages depuis la dernière visite du membre
				$retour = array(
					'image' => 'nouveau_message.png',
					'title' => 'Nouvelles réponses, jamais participé',
					'fleche' => '1'
				);
			}
		}
		else
		{
			// Si on a déjà posté dans ce sujet
			if(($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu)
			{
				// Si il n'y a pas de nouveau message depuis la dernière visite du membre
				$retour = array(
				'image' => 'repondu_pas_nouveau_message.png',
				'title' => 'Pas de nouvelles réponses, participé',
				'fleche' => '0'
				);
			}
			else
			{
				// Si il y a un ou des nouveaux messages depuis la dernière visite du membre
				$retour = array(
				'image' => 'repondu_nouveau_message.png',
				'title' => 'Nouvelles réponses, participé',
				'fleche' => '1'
				);
			}
		}
	}
	else
	{
		$retour = array(
		'image' => 'nouveau_message.png',
		'title' => 'Nouvelles réponses, jamais participé',
		'fleche' => '0'
		);
	}
	return $retour;
}

// Liste les id et titres des sujets du forum
function ListerSujetsId($forums)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!empty($forums) && is_array($forums))
	{
		$stmt = $dbh->prepare("
			SELECT sujet_id, sujet_titre
			FROM zcov2_forum_sujets
			WHERE sujet_forum_id IN(".implode(',', $forums).")
			AND sujet_corbeille = 0
			ORDER BY sujet_date DESC
		");

		$stmt->execute();

		return $stmt->fetchAll();
	}
	else
		return false;
}

//DEPRECATED / TODO : supprimer son utilisation
function VerifierValiditeForum($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Vérification de l'existence du forum, et vérification des droits de lecture pour ce forum.
	$stmt = $dbh->prepare("
	SELECT forum_id
	FROM zcov2_forum_forums
	WHERE forum_id = :f");
	$stmt->bindParam(':f', $id);

	$stmt->execute();

	$resultat = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if(!empty($resultat['forum_id']) AND verifier('voir_sujets', $resultat['forum_id']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function ListerSujetsIn($in)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT sujet_id, sujet_titre, sujet_sous_titre, " .
			"sujet_reponses, sujet_auteur, " .
			"Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
			"Ma.utilisateur_id AS sujet_auteur_pseudo_existe, " .
			"Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, " .
			"COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, " .
			"COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, " .
			"message_auteur AS sujet_dernier_message_auteur_id, " .
			"sujet_date, message_date, sujet_dernier_message, sujet_sondage, " .
			"sujet_annonce, sujet_ferme, sujet_resolu, message_id, " .
			"cat_id, cat_nom " .
			"FROM zcov2_forum_sujets " .
			"LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
			"LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id " .
			"LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id " .
			"LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id " .
			"WHERE sujet_id IN(".implode(', ', $in).") " .
			"ORDER BY sujet_annonce DESC, message_date DESC");
	$stmt->execute();
	return $stmt->fetchAll();
}

function ListerSujetsTitre($titre)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT sujet_id, sujet_titre, sujet_sous_titre, " .
			"sujet_reponses, sujet_auteur, " .
			"Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
			"Ma.utilisateur_id AS sujet_auteur_pseudo_existe, " .
			"Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, " .
			"COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, " .
			"COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, " .
			"message_auteur AS sujet_dernier_message_auteur_id, " .
			"sujet_date, message_date, sujet_dernier_message, sujet_sondage, " .
			"sujet_annonce, sujet_ferme, sujet_resolu, message_id, " .
			"cat_id, cat_nom " .
			"FROM zcov2_forum_sujets " .
			"LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
			"LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id " .
			"LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id " .
			"LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id " .
			"WHERE sujet_titre LIKE ".$dbh->quote('%'.$titre.'%')." " .
			"ORDER BY sujet_annonce DESC, message_date DESC");
	$stmt->execute();
	return $stmt->fetchAll();
}