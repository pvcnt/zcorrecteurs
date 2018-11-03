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

function ListerMP($recherche, $page, $NombreDePages, $membre = null)
{
	if(empty($membre))
	{
		$membre = $_SESSION['id'];
	}
	$nbMpParPage = 30;
	$debut = ($NombreDePages-$page) * $nbMpParPage;
	$dbh = Doctrine_Manager::connection()->getDbh();
	$in = array();
	$add = '';
	if($_GET['id'] !== '')
	{
		$add .= ' AND mp_participant_mp_dossier_id = :dossier_id';
	}
	if (isset($recherche))
	{
		$add .= ' AND (mp_titre LIKE :recherche OR mp_sous_titre LIKE :recherche)';
	}

	$stmt = $dbh->prepare("
	SELECT mp_id
	FROM zcov2_mp_participants
	LEFT JOIN zcov2_utilisateurs ON mp_participant_id = utilisateur_id
	LEFT JOIN zcov2_mp_mp ON mp_participant_mp_id = mp_id
	LEFT JOIN zcov2_mp_messages ON zcov2_mp_mp.mp_dernier_message_id = zcov2_mp_messages.mp_message_id
	WHERE mp_participant_id = :id".$add."
	AND mp_participant_statut <> ".MP_STATUT_SUPPRIME."
	ORDER BY mp_message_date DESC, mp_dernier_message_id DESC
	LIMIT ".$debut." , ".$nbMpParPage);
	$stmt->bindParam(':id', $membre);
	if (isset($recherche))
	{
		$stmt->bindValue(':recherche', '%'.$recherche.'%');
	}
	if($_GET['id'] !== '')
	{
		$stmt->bindParam(':dossier_id', $_GET['id']);
	}

	$stmt->execute();

	while($data = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		if(!empty($data['mp_id']))
		{
			$in[] = $data['mp_id'];
		}
	}
	$stmt->closeCursor();
	if (!empty($in))
	{
		$stmt = $dbh->prepare("
		SELECT mp_id, mp_titre, mp_sous_titre, mp_date, mp_dernier_message_id, mp_reponses, mp_ferme, A.mp_participant_id, B.utilisateur_pseudo AS mp_participant_pseudo, A.mp_participant_statut, E.groupe_class AS mp_participant_groupe_class, mp_message_date AS mp_dernier_message_date, mp_message_auteur_id AS mp_dernier_message_auteur, D.utilisateur_pseudo AS mp_dernier_message_pseudo, F.groupe_class AS mp_dernier_message_auteur_groupe_class, A.mp_participant_dernier_message_lu AS mp_lunonlu_participant_message_id, C.mp_participant_dernier_message_lu AS mp_lunonlu_actuel_dernier_message_lu
		FROM zcov2_mp_participants A
		LEFT JOIN zcov2_mp_mp ON A.mp_participant_mp_id = zcov2_mp_mp.mp_id
		LEFT JOIN zcov2_mp_participants C ON zcov2_mp_mp.mp_id = C.mp_participant_mp_id AND ".$membre." = C.mp_participant_id
		LEFT JOIN zcov2_mp_messages ON zcov2_mp_mp.mp_dernier_message_id = zcov2_mp_messages.mp_message_id
		LEFT JOIN zcov2_utilisateurs B ON A.mp_participant_id = B.utilisateur_id
		LEFT JOIN zcov2_utilisateurs D ON mp_message_auteur_id = D.utilisateur_id
		LEFT JOIN zcov2_groupes E ON B.utilisateur_id_groupe = E.groupe_id
		LEFT JOIN zcov2_groupes F ON D.utilisateur_id_groupe = F.groupe_id
		WHERE A.mp_participant_mp_id IN(".implode(', ', $in).")
		ORDER BY mp_dernier_message_date DESC, mp_dernier_message_id DESC,
		mp_participant_statut DESC, mp_participant_pseudo ASC");

		$stmt->execute();

		$liste = $stmt->fetchAll();
		$stmt->closeCursor();
		$Participants = array();

		$ids = array();

		foreach($liste as $cle => $valeur)
		{
			$Participants[$valeur['mp_id']][] = array(
			'mp_participant_mp_id' => $valeur['mp_id'],
			'mp_participant_id' => $valeur['mp_participant_id'],
			'utilisateur_pseudo' => $valeur['mp_participant_pseudo'],
			'groupe_class' => $valeur['mp_participant_groupe_class'],
			'mp_participant_statut' => $valeur['mp_participant_statut'],
			'mp_lunonlu_participant_message_id' => $valeur['mp_lunonlu_participant_message_id']);
			if(in_array($valeur['mp_id'], $ids))
			{
				unset($liste[$cle]);
			}
			else
			{
				$ids[] = $valeur['mp_id'];
			}
		}
		return array($liste, $Participants);
	}
	return array(array(), array());
}

function CompterMP($membre = null)
{
	if(empty($membre))
	{
		$membre = $_SESSION['id'];
	}
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_mp_mp
	LEFT JOIN zcov2_mp_participants ON zcov2_mp_mp.mp_id = zcov2_mp_participants.mp_participant_mp_id
	WHERE mp_participant_id = :id AND mp_participant_statut >= 0");
	$stmt->bindParam(':id', $membre);

	$stmt->execute();

	return $stmt->fetchColumn();
}

//Cette fonction retourne l'image du système lu/non lu.
function LuNonlu($lu)
{
	if(!empty($lu['mp_lunonlu_message_id']))
	{
		$dejavu = true;
	}
	else
	{
		$dejavu = false;
	}
	if($dejavu)
	{
		if($lu['mp_lunonlu_message_id'] == $lu['mp_dernier_message_id'])
		{
			//Si il n'y a pas de nouveau message depuis la dernière visite du membre
			$retour = array(
			'image' => 'pas_nouveau_message.png',
			'title' => 'Pas de nouvelles réponses',
			'fleche' => '0'
			);
		}
		else
		{
			//Si il y a un ou des nouveaux messages depuis la dernière visite du membre
			$retour = array(
			'image' => 'nouveau_message.png',
			'title' => 'Nouvelles réponses',
			'fleche' => '1'
			);
		}
	}
	else
	{
		$retour = array(
		'image' => 'nouveau_message.png',
		'title' => 'Nouvelles réponses',
		'fleche' => '0'
		);
	}
	return $retour;
}
?>
