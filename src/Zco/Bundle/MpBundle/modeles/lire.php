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
// +----------------------------------------------------------------------+
// | Copyright (c) www.zcorrecteurs.fr 2008                               |
// +----------------------------------------------------------------------+
// | Modèle concernant tout ce qui est listage des MP                     |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 06 septembre 2008                         |
// | Dernière modification le : 09 septembre 2008                         |
// +----------------------------------------------------------------------+

function InfoMP()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_id, mp_participant_mp_id, mp_titre, mp_sous_titre, mp_date, mp_premier_message_id, mp_dernier_message_id, mp_reponses, mp_ferme, mp_participant_dernier_message_lu AS mp_lunonlu_message_id, mp_participant_statut, mp_participant_mp_dossier_id, mp_alerte_id, mp_alerte_resolu
	FROM zcov2_mp_mp
	LEFT JOIN zcov2_mp_participants ON mp_id = mp_participant_mp_id AND mp_participant_id = :user_id AND mp_participant_statut >= 0
	LEFT JOIN zcov2_mp_alertes ON mp_id = mp_alerte_mp_id AND mp_alerte_resolu = 0
	WHERE mp_id = :mp_id ");

	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->execute();

	$retour = $stmt->fetch(PDO::FETCH_ASSOC);

	if(empty($retour['mp_participant_mp_id']) AND !verifier('mp_alertes'))
	{
		return false;
	}
	elseif(!empty($retour['mp_alerte_id']) AND verifier('mp_alertes') AND !$retour['mp_alerte_resolu'])
	{
		return $retour;
	}
	elseif(empty($retour['mp_participant_mp_id']) AND verifier('mp_espionner'))
	{
		return $retour;
	}
	elseif(!empty($retour['mp_participant_mp_id']))
	{
		return $retour;
	}
	else
	{
		return false;
	}
}

function InfoMessage($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_id, mp_titre, mp_ferme, mp_message_id, mp_message_auteur_id, mp_message_texte, utilisateur_pseudo, mp_participant_id, mp_alerte_id
	FROM zcov2_mp_messages
	LEFT JOIN zcov2_mp_mp ON mp_message_mp_id = mp_id
	LEFT JOIN zcov2_mp_participants ON mp_participant_mp_id = mp_message_mp_id AND mp_participant_id = :user_id AND mp_participant_statut >= ".MP_STATUT_NORMAL."
	LEFT JOIN zcov2_utilisateurs ON mp_message_auteur_id = utilisateur_id
	LEFT JOIN zcov2_mp_alertes ON mp_id = mp_alerte_mp_id AND mp_alerte_resolu = 0
	WHERE mp_message_id = :mp_message_id");

	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':mp_message_id', $id);
	$stmt->execute();

	$retour = $stmt->fetch(PDO::FETCH_ASSOC);
	if(empty($retour['mp_participant_id']) AND verifier('mp_alertes') AND !empty($retour['mp_alerte_id']))
	{
		return $retour;
	}
	elseif(!empty($retour['mp_participant_id']))
	{
		return $retour;
	}
	else
	{
		return false;
	}
}

function ListerMessages($page, $rev = false)
{
	$nbMessagesParPage = 20;
	$debut = ($page - 1) * $nbMessagesParPage;

	if($_GET['p'] > 1)
	{
		$debut--;
		$nombreDeMessagesAafficher = $nbMessagesParPage+1;
	}
	else
	{
		$nombreDeMessagesAafficher = $nbMessagesParPage;
	}

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("
	SELECT utilisateur_sexe, mp_message_id, mp_message_mp_id, mp_message_auteur_id, utilisateur_pseudo, mp_message_date, 
	mp_message_texte, mp_participant_statut, groupe_nom, groupe_class, groupe_logo, groupe_logo_feminin, utilisateur_avatar, 
	utilisateur_nb_sanctions, utilisateur_pourcentage, mp_message_ip, utilisateur_signature, utilisateur_citation, 
	utilisateur_titre, 

	CASE WHEN connecte_derniere_action >= NOW() - INTERVAL ".NOMBRE_MINUTES_CONNECTE." MINUTE
	THEN 'online.png'
	ELSE 'offline.png'
	END AS statut_connecte,

	CASE WHEN connecte_derniere_action >= NOW() - INTERVAL ".NOMBRE_MINUTES_CONNECTE." MINUTE
	THEN 'En ligne'
	ELSE 'Hors ligne'
	END AS statut_connecte_label

	FROM zcov2_mp_messages
	LEFT JOIN zcov2_mp_participants ON zcov2_mp_messages.mp_message_mp_id = zcov2_mp_participants.mp_participant_mp_id AND mp_participant_id = mp_message_auteur_id
	LEFT JOIN zcov2_utilisateurs ON mp_message_auteur_id = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	LEFT JOIN zcov2_connectes ON connecte_id_utilisateur = utilisateur_id
	WHERE mp_message_mp_id = :mp_id
	ORDER BY mp_message_date ".($rev ? 'DESC' : 'ASC')."
	LIMIT ".$debut." , ".$nombreDeMessagesAafficher);

	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->execute();
	return $stmt->fetchAll();
}

function TrouverLaPageDeCeMessage($Message)
{
	//Dès qu'il y a un paramètre de message dans l'URL, cette fonction est appelée pour trouver sur quelle page du MP se trouve le message concerné.
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_message_id
	FROM zcov2_mp_messages
	WHERE mp_message_mp_id = :mp
	ORDER BY mp_message_date ASC");
	$stmt->bindParam(':mp', $_GET['id']);

	$stmt->execute();
	$resultat = $stmt->fetchAll();

	//On calcule le nombre total de messages.
	$totalDesMessages = 0;
	foreach($resultat as $valeur)
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
			if($valeur["mp_message_id"] != $Message)
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
	$nombreDePages = ceil($totalDesMessages / $nbMessagesParPage); //On calcule le nombre de pages qu'il va y avoir.
	$PageCible = ceil($i/$nbMessagesParPage); //Voici la bonne page sur laquelle on va envoyer le visiteur.
	if($PositionDansLaPage == $nbMessagesParPage AND $PageCible < $nombreDePages)
	{
		$PageCible++;
	}
	//echo 'Total Des Messages '.$totalDesMessages.' nbpages '.$nombreDePages.' pagecible '.$PageCible.' PositionDansLaPage '.$PositionDansLaPage;
	//exit();
	return $PageCible;
}

function MarquerMPLu($mp_id)
{
	$mp = InfoMP($mp_id);

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('UPDATE zcov2_mp_participants '
		.'SET mp_participant_dernier_message_lu = :message_id '
		.'WHERE mp_participant_id = :user_id '
		.'AND mp_participant_mp_id = :mp_id');
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':mp_id', $mp_id);
	$stmt->bindParam(':message_id', $mp['mp_dernier_message_id']);
	$stmt->execute();

	Container::getService('zco_core.cache')->Set('MPnonLu'.$_SESSION['id'], 1);
}

function RendreLeMPLu($mp_id, $nombreDePages, $dernier_message, $ListerMessages, $InfosLuNonlu)
{
	if (!empty($InfosLuNonlu['mp_lunonlu_message_id']))
	{
		$dejavu = true;
	}
	else
	{
		$dejavu = false;
	}
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Si on est sur la page la plus récente, on considère que le MP entier est lu (jusqu'à son dernier message)
	if($_GET['p'] == $nombreDePages)
	{
		if($InfosLuNonlu['mp_lunonlu_message_id'] != $dernier_message)
		{
			//On met à jour
			$stmt = $dbh->prepare("UPDATE zcov2_mp_participants
			SET mp_participant_dernier_message_lu = :message_id
			WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
			$stmt->bindParam(':user_id', $_SESSION['id']);
			$stmt->bindParam(':mp_id', $mp_id);
			$stmt->bindParam(':message_id', $dernier_message);

			$stmt->execute();

			$stmt->closeCursor();
			Container::getService('zco_core.cache')->Set('MPnonLu'.$_SESSION['id'], 1);
			if($_SESSION['MPsnonLus'] > 0)
				$_SESSION['MPsnonLus']--;
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
				$MessageLePlusRecentDansLaPage = $valeur['mp_message_id'];
			}
			$i++;
		}
		if(!isset($MessageLePlusRecentDansLaPage))
			$MessageLePlusRecentDansLaPage = $valeur['mp_message_id'];

		//Ok, maintenant on a le dernier message de la page courante :)
		//On vérifie avant bien sûr que la mise à jour est nécessaire.
		//Sinon on ne la fait pas :)
		//La condition suivante nous économise quand-même une requête UPDATE quand elle est inutile ;)
		if($InfosLuNonlu['mp_lunonlu_message_id'] < $MessageLePlusRecentDansLaPage)
		{
			//On met à jour
			$stmt = $dbh->prepare("UPDATE zcov2_mp_participants
			SET mp_participant_dernier_message_lu = :message_id
			WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
			$stmt->bindParam(':user_id', $_SESSION['id']);
			$stmt->bindParam(':mp_id', $mp_id);
			$stmt->bindParam(':message_id', $MessageLePlusRecentDansLaPage);

			$stmt->execute();

			$stmt->closeCursor();
		}
	}
	return true;
}
?>
