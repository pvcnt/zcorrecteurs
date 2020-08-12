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
// +----------------------------------------------------------------------+
// | Copyright (c) www.zcorrecteurs.fr 2008                               |
// +----------------------------------------------------------------------+
// | Modèle concernant tout ce qui concerne les participants d'un MP      |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 07 septembre 2008                         |
// | Dernière modification le : 07 septembre 2008                         |
// +----------------------------------------------------------------------+

function InfoParticipant()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_participant_id, mp_participant_statut, utilisateur_pseudo
	FROM zcov2_mp_participants
	LEFT JOIN zcov2_utilisateurs ON mp_participant_id = utilisateur_id
	WHERE mp_participant_mp_id = :mp_id AND mp_participant_id = :user_id");

	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_GET['id2']);
	$stmt->execute();

	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function VerifierParticipantExiste($pseudo)
{
	if($pseudo != PSEUDO_COMPTE_AUTO)
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		//On recherche le participant
		$stmt = $dbh->prepare("SELECT utilisateur_id FROM zcov2_utilisateurs
		WHERE utilisateur_pseudo = :pseudo");
		$stmt->bindParam(':pseudo', $pseudo);
		$stmt->execute();

		$resultat = $stmt->fetchColumn();
		if(!empty($resultat) AND is_numeric($resultat))
		{
			return $resultat;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function ListerParticipants($mp_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_participant_id, mp_participant_mp_dossier_id, mp_participant_statut, utilisateur_pseudo, groupe_class, mp_participant_dernier_message_lu AS mp_lunonlu_message_id
	FROM zcov2_mp_participants
	LEFT JOIN zcov2_utilisateurs ON mp_participant_id = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE mp_participant_mp_id = :mp_id
	ORDER BY mp_participant_statut DESC, utilisateur_pseudo ASC");

	$stmt->bindParam(':mp_id', $mp_id);
	$stmt->execute();

	return $stmt->fetchAll();
}

function AjouterParticipant()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On recherche l'id de l'user à partir du pseudo fourni et on vérifie qu'il n'a pas atteint son quota.
	$stmt = $dbh->prepare("
	SELECT utilisateur_id, COUNT(mp_participant_mp_id) AS nb, utilisateur_id_groupe
	FROM zcov2_utilisateurs
	LEFT JOIN zcov2_mp_participants ON mp_participant_id = utilisateur_id AND mp_participant_statut >= ".MP_STATUT_NORMAL."
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE utilisateur_pseudo = :user_pseudo
	GROUP BY utilisateur_id");
	$stmt->bindParam(':user_pseudo', $_POST['pseudo']);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$result['groupe_mp_quota'] = verifier('mp_quota', 0, $result['utilisateur_id_groupe']);
	if(empty($result['utilisateur_id']) OR $result['utilisateur_id'] == ID_COMPTE_AUTO)
	{
		return 266;
	}
	elseif($result['nb']+1 > $result['groupe_mp_quota'] AND $result['groupe_mp_quota'] != -1 AND !verifier('mp_tous_droits_participants'))
	{
		return 267;
	}
	else
	{
		$stmt = $dbh->prepare('SELECT COUNT(*) FROM zcov2_mp_participants
			WHERE mp_participant_mp_id = ?
			AND mp_participant_id = ?
			AND mp_participant_statut >= '.MP_STATUT_NORMAL);
		$stmt->execute(array($_GET['id'], $result['utilisateur_id']));

		if ($stmt->fetchColumn() > 0)
			return 266;

		$stmt = $dbh->prepare("REPLACE INTO zcov2_mp_participants (mp_participant_mp_id, mp_participant_id, mp_participant_statut) VALUES (:mp_id, :user_id, :statut)");
		$statut = isset($_POST['master']);
		$stmt->bindParam(':mp_id', $_GET['id']);
		$stmt->bindParam(':user_id', $result['utilisateur_id']);
		$stmt->bindParam(':statut', $statut);

		if($stmt->execute())
		{
			Container::getService('zco_core.cache')->save('MPnonLu'.$result['utilisateur_id'], true, strtotime('+1 hour'));
			return 265;
		}
		else
		{
			return 266;
		}
	}
}

function SupprimerParticipant()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_participants SET mp_participant_statut = ".MP_STATUT_SUPPRIME."
	WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_GET['id2']);

	$stmt->execute();

	Container::getService('zco_core.cache')->save('MPnonLu'.$_GET['id2'], true, strtotime('+1 hour'));

	return true;
}

function MaitreConversation()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_participants SET mp_participant_statut = ".MP_STATUT_MASTER."
	WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_GET['id2']);

	$stmt->execute();

	return true;
}

function PlusMaitreConversation()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_participants SET mp_participant_statut = ".MP_STATUT_NORMAL."
	WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_GET['id2']);

	$stmt->execute();

	return true;
}
