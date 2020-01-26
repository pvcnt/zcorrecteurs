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
// | Modèle concernant la création de nouvelle réponse et de nouveau MP   |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 07 septembre 2008                         |
// | Dernière modification le : 09 septembre 2008                         |
// +----------------------------------------------------------------------+

function AjouterMP()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On crée le nouveau MP
	$stmt = $dbh->prepare('INSERT INTO zcov2_mp_mp ('
		.'mp_titre, mp_sous_titre, mp_date, mp_ferme '
		.') VALUES (:titre, :sous_titre, NOW(), :mp_ferme)');
	$stmt->bindParam(':titre', $_POST['titre']);
	$stmt->bindParam(':sous_titre', $_POST['sous_titre']);

	$fermer = verifier('mp_fermer') && isset($_POST['ferme']);

	$stmt->bindValue(':mp_ferme', (int)$fermer);

	$stmt->execute();

	//On récupère l'id du MP nouvellement créé.
	$NouveauMPID = $dbh->lastInsertId();

	$stmt->closeCursor();

	//On crée le message
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_messages (mp_message_mp_id, mp_message_auteur_id, mp_message_date, mp_message_texte, mp_message_ip)
	VALUES (:NouveauMPID, :auteur, NOW(), :texte, :ip)");
	$stmt->bindParam(':NouveauMPID', $NouveauMPID);
	$stmt->bindParam(':auteur', $_SESSION['id']);
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp(true)));
	$stmt->execute();

	//On récupère l'id de du message nouvellement créé.
	$NouveauMessageID = $dbh->lastInsertId();

	$stmt->closeCursor();

	//Grâce au numéro du message récupéré, on peut updater la table des MP pour indiquer que ce message est le premier et le dernier du MP.
	$stmt = $dbh->prepare("UPDATE zcov2_mp_mp
	SET mp_premier_message_id = :NouveauMessageID, mp_dernier_message_id = :NouveauMessageID
	WHERE mp_id = :NouveauMPID");
	$stmt->bindParam(':NouveauMessageID', $NouveauMessageID);
	$stmt->bindParam(':NouveauMPID', $NouveauMPID);
	$stmt->execute();
	$stmt->closeCursor();

	//Création des participants

	//On va d'abord préparer la requête
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_participants (mp_participant_mp_id, mp_participant_mp_dossier_id, mp_participant_id, mp_participant_statut, mp_participant_dernier_message_lu)
	VALUES (:mp_id, :dossier_id, :participant_id, :statut, :dernier_msg_lu)");
	$stmt->bindParam(':mp_id', $NouveauMPID); //Ce paramètre ne changera pour aucun des participants : on ne le définit qu'une fois.

	//On ajoute déjà le créateur du MP comme participant avec le statut de MP_STATUT_OWNER
	$stmt->bindParam(':dossier_id', $_POST['dossier']);
	$stmt->bindParam(':participant_id', $_SESSION['id']);
	$stmt->bindValue(':statut', MP_STATUT_OWNER);
	$stmt->bindParam(':dernier_msg_lu', $NouveauMessageID);
	$stmt->execute();

	//Puis, pour chaque participant, on va l'ajouter en BDD.
	$stmt->bindValue(':dossier_id', 0); //On mettra ce MP dans le dossier accueil de tous les participants
	$stmt->bindValue(':dernier_msg_lu', 0); //Le MP sera non-lu pour tous les autres participants
	foreach($_POST['participants'] as &$valeur)
	{
		$stmt->bindParam(':participant_id', $valeur);
		$stmt->bindValue(':statut', MP_STATUT_NORMAL);
		$stmt->execute();

		// Eventuelle notification par email
		NotifierParticipant($NouveauMPID, $valeur, $_POST['titre']);
	}
	$stmt->closeCursor();
	return $NouveauMPID;
}

function AjouterReponse()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$InfoMp = InfoMp();
	$ListerParticipants = ListerParticipants($_GET['id']);

	//On insère la réponse
	$stmt = $dbh->prepare("
	INSERT INTO zcov2_mp_messages (mp_message_mp_id, mp_message_auteur_id, mp_message_date, mp_message_texte, mp_message_ip)
	VALUES (:mp_id, :user_id, NOW(), :texte, :ip)");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp(true)));

	$stmt->execute();

	//On récupère l'id du message créé
	$nouveau_message_id = $dbh->lastInsertId();

	//On met à jour le MP
	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_mp SET mp_dernier_message_id = :dernier_msg, mp_reponses = mp_reponses+1
	WHERE mp_id = :mp_id");
	$stmt->bindParam(':dernier_msg', $nouveau_message_id);
	$stmt->bindParam(':mp_id', $_GET['id']);

	$stmt->execute();

	//On met à jour le dernier message lu
	$stmt = $dbh->prepare("
	UPDATE zcov2_mp_participants SET mp_participant_dernier_message_lu = :dernier_msg
	WHERE mp_participant_id = :user_id AND mp_participant_mp_id = :mp_id");
	$stmt->bindParam(':dernier_msg', $nouveau_message_id);
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':mp_id', $_GET['id']);

	$stmt->execute();

	// Eventuelle notification par email
	foreach($ListerParticipants as &$participant)
	{
		if($participant['mp_participant_id'] != $_SESSION['id'])
			NotifierParticipant($_GET['id'],
				$participant['mp_participant_id'],
				$InfoMp['mp_titre']);
	}

	return $nouveau_message_id;
}

function EditerReponse()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$InfoMp = InfoMessage($_GET['id']);

	//On edite la réponse
	$stmt = $dbh->prepare("UPDATE zcov2_mp_messages	SET mp_message_texte = :texte WHERE mp_message_id = :msg_id");
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindParam(':msg_id', $_GET['id']);

	$stmt->execute();
}

function RevueMP()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("
	SELECT mp_message_id, mp_message_mp_id, mp_message_auteur_id, utilisateur_pseudo, utilisateur_citation, 
	utilisateur_sexe, mp_message_date, mp_message_texte, groupe_nom, groupe_class, groupe_logo, groupe_logo_feminin, 
	utilisateur_avatar, utilisateur_signature, utilisateur_titre
	FROM zcov2_mp_participants
	LEFT JOIN zcov2_mp_messages ON zcov2_mp_participants.mp_participant_mp_id = zcov2_mp_messages.mp_message_mp_id
	LEFT JOIN zcov2_utilisateurs ON mp_message_auteur_id = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE mp_message_mp_id = :mp_id AND mp_participant_id = mp_message_auteur_id
	ORDER BY mp_message_date DESC
	LIMIT 0, 15");

	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->execute();

	return $stmt->fetchAll();
}

function NotifierParticipant($mp, $participant, $titre)
{
	$infosParticipant = InfosUtilisateur($participant);
	if($infosParticipant['preference_activer_email_mp'])
	{
		$objet = '[zCorrecteurs.fr] Nouveau message privé';
		$message = render_to_string('::mail_auto/nouveau_mp.html.php', array(
			'pseudo'        => $infosParticipant['utilisateur_pseudo'],
			'auteur_id'     => $_SESSION['id'],
			'auteur_pseudo' => $_SESSION['pseudo'],
			'titre'         => $titre,
			'id'            => $mp,
		));

		send_mail($infosParticipant['utilisateur_email'],
			htmlspecialchars($infosParticipant['utilisateur_pseudo']),
			$objet, $message);
	}
}
