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
// +----------------------------------------------------------------------+
// | Copyright (c) www.zcorrecteurs.fr 2008                               |
// +----------------------------------------------------------------------+
// | Modèle concernant le traitement des actions étendues à plusieurs MP  |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 15 septembre 2008                         |
// | Dernière modification le : 15 septembre 2008                         |
// +----------------------------------------------------------------------+

function OuvrirMP($MP)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!is_array($MP))
	{
		$MP = array($MP);
	}
	$MP = implode($MP,',');
	$stmt = $dbh->prepare("UPDATE zcov2_mp_mp SET mp_ferme = 0 WHERE mp_id IN (".$MP.") AND mp_ferme = 1");
	$stmt->execute();

	return true;
}

function FermerMP($MP)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!is_array($MP))
	{
		$MP = array($MP);
	}
	$MP = implode($MP,',');
	$stmt = $dbh->prepare("UPDATE zcov2_mp_mp SET mp_ferme = 1 WHERE mp_id IN (".$MP.") AND mp_ferme = 0");
	$stmt->execute();

	return true;
}

function RendreMPLus($MP, $ListerMP)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!is_array($MP))
	{
		$MP = array($MP);
	}

	//On va recueillir, pour chaque MP coché, deux informations essentielles : le dernier message de ce MP, et le dernier message lu par le type.
	$MPCochesInfos = array();
	foreach($MP as $valeur)
	{
		foreach($ListerMP as $valeur2)
		{
			if($valeur2['mp_id'] == $valeur)
			{
				$MPCochesInfos[$valeur]['mp_dernier_message_id'] = $valeur2['mp_dernier_message_id'];
				$MPCochesInfos[$valeur]['mp_lunonlu_actuel_dernier_message_lu'] = $valeur2['mp_lunonlu_actuel_dernier_message_lu'];
			}
		}
	}

	//Ok, on a notre tableau $MPCochesInfos qui contient les infos dont nous avons besoin.
	//Grâce à ce tableau, nous allons déterminer quels MP doivent entraîner un UPDATE, et lesquels on ne doit RIEN faire car certains MP cochés peuvent être déjà entièrement lus.
	$Insert = array();
	$Update = array();
	foreach($MPCochesInfos as $clef => $valeur)
	{
		if($valeur['mp_lunonlu_actuel_dernier_message_lu'] < $valeur['mp_dernier_message_id'])
		{
			$Update[] = $clef;
		}
		//Pour les autres on a rien à modifier, ils sont déjà lus !
	}
	if(!empty($Update[0]))
	{
		//On prépare la requête d'update
		$stmt = $dbh->prepare("UPDATE zcov2_mp_participants SET mp_participant_dernier_message_lu = :dernier_msg_id
		WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
		$stmt->bindParam(':user_id', $_SESSION['id']);
		//Pour chaque MP à rendre lu, on update son enregistrement dans la table zcov2_mp_participants
		foreach($Update as &$valeur)
		{
			$stmt->bindParam(':mp_id', $valeur);
			$stmt->bindParam(':dernier_msg_id', $MPCochesInfos[$valeur]['mp_dernier_message_id']);
			$stmt->execute();
		}
	}
	return true;
}

function RendreMPNonLus($MP)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!is_array($MP))
	{
		$MP = array($MP);
	}
	$MP = implode($MP,',');
	$stmt = $dbh->prepare("UPDATE zcov2_mp_participants SET mp_participant_dernier_message_lu = 0 WHERE mp_participant_id = :user_id AND mp_participant_mp_id IN (".$MP.")");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->execute();

	return true;
}

function DeplacerMP($MP, $dossier_cible)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$dossier_cible = intval($dossier_cible);
	if(!is_array($MP))
	{
		$MP = array($MP);
	}
	$MP = implode($MP,',');
	$stmt = $dbh->prepare("UPDATE zcov2_mp_participants
	SET mp_participant_mp_dossier_id = :dossier_cible
	WHERE mp_participant_mp_id IN (".$MP.") AND mp_participant_id = :user_id AND mp_participant_mp_dossier_id <> :dossier_cible");
	$stmt->bindParam(':dossier_cible', $dossier_cible);
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->execute();

	return true;
}

//Suppression d'UN MP
function SupprimerMP($MP, $InfoMP, $ListerParticipants)
{
	$dbh = Doctrine_Manager::connection()->getDbh();


	/*On doit déterminer si on va supprimer le MP complètement de la base de données, ou si on va juste retirer le participant de la 	conversation.
	Cela dépend de plusieurs conditions.
	ON VA SUPPRIMER UN MP COMPLÈTEMENT DE LA BDD SI :
		=> Il ne reste plus qu'un participant, ET il veut supprimer le MP.
		OU
		=> Celui qui veut supprimer est le créateur du MP ET il n'y a que deux participants en tout, ET le deuxième participant n'a pas DU TOUT 		encore lu le MP
	SINON :
		=> On enlève juste le participant.*/

	$NombreParticipants = 0;
	foreach($ListerParticipants as $valeur)
	{
		if($valeur['mp_participant_statut'] != MP_STATUT_SUPPRIME)
		{
			$NombreParticipants++;
		}
	}
	if($NombreParticipants == 1 OR ($InfoMP['mp_participant_statut'] == MP_STATUT_OWNER AND $NombreParticipants == 2 AND empty($ListerParticipants[1]['mp_lunonlu_message_id'])))
	{
		//On supprime le MP intégral.

		//On supprime le MP.
		$stmt = $dbh->prepare("DELETE FROM zcov2_mp_mp
		WHERE mp_id = :mp_id");
		$stmt->bindParam(':mp_id', $_GET['id']);

		$stmt->execute();

		$stmt->closeCursor();

		//On supprime les messages du MP.
		$stmt = $dbh->prepare("DELETE FROM zcov2_mp_messages
		WHERE mp_message_mp_id = :mp_message_mp_id");
		$stmt->bindParam(':mp_message_mp_id', $_GET['id']);

		$stmt->execute();

		$stmt->closeCursor();

		//On supprime tous les participants du MP
		$stmt = $dbh->prepare("DELETE FROM zcov2_mp_participants
		WHERE mp_participant_mp_id = :mp_participant_mp_id");
		$stmt->bindParam(':mp_participant_mp_id', $_GET['id']);

		$stmt->execute();

		$stmt->closeCursor();
	}
	else
	{
		//On supprime juste le participant
		$_GET['id2'] = $_SESSION['id'];
		SupprimerParticipant();
	}
}

//Suppression MULTIPLE de MP
function SupprimerMultipleMP($MP)
{
	if(!is_array($MP))
	{
		$MP = array($MP);
	}
	$MP = implode($MP,',');
	$dbh = Doctrine_Manager::connection()->getDbh();
	//Pour chaque MP coché, on compte le nombre de participants.
	$stmt = $dbh->prepare("
	SELECT mp_participant_mp_id, COUNT( mp_participant_id ) AS nb_participants
	FROM zcov2_mp_participants
	WHERE mp_participant_mp_id IN ( ".$MP." ) AND mp_participant_statut <> ".MP_STATUT_SUPPRIME."
	GROUP BY mp_participant_mp_id");
	$stmt->execute();


	$NbParticipants = $stmt->fetchAll();
	$stmt->closeCursor();

	//Pour chaque MP coché, on relève le statut de celui qui demande la suppression dans ce MP.
	$stmt = $dbh->prepare("
	SELECT mp_participant_mp_id, mp_participant_statut
	FROM zcov2_mp_participants
	WHERE mp_participant_mp_id IN ( ".$MP." ) AND mp_participant_id = :user_id");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->execute();

	$StatutSupprimeurFaux = $stmt->fetchAll();
	$stmt->closeCursor();
	$StatutSupprimeur = array();
	foreach($StatutSupprimeurFaux as $valeur)
	{
		if(in_array($valeur['mp_participant_statut'], array(MP_STATUT_NORMAL, MP_STATUT_MASTER, MP_STATUT_OWNER)))
		{
			$StatutSupprimeur[$valeur['mp_participant_mp_id']] = $valeur['mp_participant_statut'];
		}
	}
	unset($StatutSupprimeurFaux);

	//Pour chaque MP coché
	foreach($NbParticipants as $valeur)
	{
		if($valeur['nb_participants'] == 2)
		{
			//On doit trouver le dernier message lu du deuxième participant.
			$stmt = $dbh->prepare("
			SELECT mp_participant_dernier_message_lu
			FROM zcov2_mp_participants
			WHERE mp_participant_mp_id = :mp_id AND mp_participant_id <> :user_id");
			$stmt->bindParam(':mp_id', $valeur['mp_participant_mp_id']);
			$stmt->bindParam(':user_id', $_SESSION['id']);
			$stmt->execute();

			$DernierMessageLuDeuxiemeParticipant = $stmt->fetchColumn();
		}

		if($valeur['nb_participants'] < 2 OR ($valeur['nb_participants'] == 2 AND $DernierMessageLuDeuxiemeParticipant == 0 AND $StatutSupprimeur[$valeur['mp_participant_mp_id']] == MP_STATUT_OWNER))
		{
			//On supprime le MP intégral

			//On supprime le MP.
			$stmt = $dbh->prepare("DELETE FROM zcov2_mp_mp
			WHERE mp_id = :mp_id");
			$stmt->bindParam(':mp_id', $valeur['mp_participant_mp_id']);

			$stmt->execute();

			$stmt->closeCursor();

			//On supprime les messages du MP.
			$stmt = $dbh->prepare("DELETE FROM zcov2_mp_messages
			WHERE mp_message_mp_id = :mp_message_mp_id");
			$stmt->bindParam(':mp_message_mp_id', $valeur['mp_participant_mp_id']);

			$stmt->execute();

			$stmt->closeCursor();

			//On supprime tous les participants du MP
			$stmt = $dbh->prepare("DELETE FROM zcov2_mp_participants
			WHERE mp_participant_mp_id = :mp_participant_mp_id");
			$stmt->bindParam(':mp_participant_mp_id', $valeur['mp_participant_mp_id']);

			$stmt->execute();

			$stmt->closeCursor();
		}
		else
		{
			//On supprime juste le participant
			$_GET['id'] = $valeur['mp_participant_mp_id'];
			$_GET['id2'] = $_SESSION['id'];
			SupprimerParticipant();
		}
	}
}

function NomsMP($ids)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$tmp = '';
	foreach($ids as $id)
	{
		$tmp .= intval($id) . ',';
	}
	$tmp = substr($tmp, 0, -1);
	$stmt = $dbh->prepare("SELECT mp_titre, mp_sous_titre, mp_id FROM zcov2_mp_mp WHERE mp_id IN (" . $tmp . ")");
	$stmt->execute();
	return $stmt->fetchAll();
}