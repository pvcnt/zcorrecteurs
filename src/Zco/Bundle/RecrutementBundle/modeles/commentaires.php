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
 * Modèle gérant les commentaires des candidatures
 *
 * @author Vanger
 */

function ListerCommentairesShoutbox($id, $page = 1)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$debut = ($page - 1) * 15;

	$stmt = $db->prepare("SELECT commentaire_id, groupe_class, groupe_logo, groupe_logo_feminin, groupe_nom,
		commentaire_date, commentaire_texte, utilisateur_id, utilisateur_avatar, utilisateur_sexe,
		IFNULL(utilisateur_pseudo, 'Anonyme') as utilisateur_pseudo
		FROM zcov2_recrutements_commentaires
		LEFT JOIN zcov2_utilisateurs ON commentaire_utilisateur_id = utilisateur_id
	    LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
		WHERE commentaire_candidature_id = :id
		ORDER BY commentaire_date ASC
		LIMIT ".$debut.", 15");
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return $stmt->fetchAll();
}

function CompterCommentairesShoutbox($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	SELECT COUNT(*) AS nb FROM zcov2_recrutements_commentaires WHERE commentaire_candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$retour = $stmt->fetchColumn();
	$stmt->closeCursor();
	return $retour;
}

/**
 * Marquer les commentaires comme lus.
 * @param integer $infos			Infos sur la candidature
 * @param integer $page				La page courante.
 * @param integer $comms			La liste des commentaires.
 */
function MarquerCommentairesLus($infos, $page, $comms)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On regarde si on a déjà lu au moins un commentaire
	$stmt = $dbh->prepare("SELECT lunonlu_commentaire_id
	FROM zcov2_recrutements_lunonlu
	WHERE lunonlu_utilisateur_id = :id_utilisateur AND lunonlu_candidature_id = :id_candidature");
	$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
	$stmt->bindParam(':id_candidature', $infos['candidature_id']);
	$stmt->execute();
	$dernier_lu = $stmt->fetchColumn();
	
	//On récupère l'id du dernier message
	$id_comm = null;
	foreach($comms as $c)
		$id_comm = $c['commentaire_id'];

	//Si l'id du dernier commentaire est supérieur à celui du dernier lu, où si aucun commentaire n'a jamais été lu
	if(empty($dernier_lu) || $id_comm > $dernier_lu)
	{
		$stmt = $dbh->prepare("INSERT INTO zcov2_recrutements_lunonlu(lunonlu_utilisateur_id, lunonlu_candidature_id, lunonlu_commentaire_id)
		VALUES(:id_utilisateur, :id_candidature, :id_comm)
		ON DUPLICATE KEY UPDATE lunonlu_commentaire_id = :id_comm");
		$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
		$stmt->bindParam(':id_candidature', $infos['candidature_id']);
		$stmt->bindParam(':id_comm', $id_comm);
		$stmt->execute();
	}
}


/**
 * Retourne la page d'un commentaire.
 * @param integer $id_comm                      L'id du commentaire.
 * @param integer $id_billet                    L'id de la candidature.
 * @return integer
 */
function TrouverPageCommentaire($id_comm, $id_com)
{
        $dbh = Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT commentaire_id " .
                        "FROM zcov2_recrutements_commentaires " .
                        "WHERE commentaire_candidature_id = :id " .
                        "ORDER BY commentaire_date");
        $stmt->bindParam(':id', $id_com);
        $stmt->execute();
        $commentaires = $stmt->fetchAll();
        $nb = 1;
        $page = 1;

        foreach($commentaires as $c)
        {
                if($nb > 15)
                {
                        $page ++;
                        $nb = 1;
                }

                if($c['commentaire_id'] == $id_comm)
                        return $page;

                $nb ++;
        }
        return false;
}


function AjouterCommentaireShoutbox($id, $texte, $id_u = null)
{
	$db = Doctrine_Manager::connection()->getDbh();
	if(is_null($id_u))
		$id_u = $_SESSION['id'];

	$stmt = $db->prepare("
	INSERT INTO zcov2_recrutements_commentaires (commentaire_candidature_id, commentaire_utilisateur_id, commentaire_date, commentaire_texte)
		VALUES(:id, :user, NOW(), :texte)");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':user', $id_u);
	$stmt->bindParam(':texte', $texte);
	$stmt->execute();

	$new_com_id = $db->lastInsertId();

	//On crée un enregistrement dans zcov2_recrutements_lunonlu ou on update selon le cas
	$stmt = $db->prepare("INSERT INTO zcov2_recrutements_lunonlu
	(lunonlu_utilisateur_id, lunonlu_candidature_id, lunonlu_commentaire_id, lunonlu_participe)
	VALUES(:user_id, :candidature_id, :commentaire_id, 1)
	ON DUPLICATE KEY UPDATE lunonlu_commentaire_id = :commentaire_id, lunonlu_participe = 1");
	$stmt->bindParam(':commentaire_id', $new_com_id);
	$stmt->bindParam(':user_id', $id_u);
	$stmt->bindParam(':candidature_id', $id);

	$stmt->execute();
	$stmt->closeCursor();

	return $new_com_id;
}

function RecupererZCodeCommentaire($id)
 {
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	SELECT commentaire_texte, utilisateur_pseudo
	FROM zcov2_recrutements_commentaires
		LEFT JOIN zcov2_utilisateurs ON commentaire_utilisateur_id = utilisateur_id
	WHERE commentaire_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$retour = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	if($retour===false)
		return array('', '');
	else
		return array($retour['commentaire_texte'], $retour['utilisateur_pseudo']);
}

function InfosCommentaire($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare('SELECT commentaire_id, commentaire_candidature_id, recrutement_etat, '
		.'commentaire_texte, commentaire_date, u1.utilisateur_pseudo AS utilisateur_pseudo, '
		.'u1.utilisateur_id AS utilisateur_id, recrutement_nom, recrutement_id, '
		.'candidature_id, u1.utilisateur_pseudo AS utilisateur_pseudo, u2.utilisateur_pseudo AS postulant_pseudo '
		.'FROM zcov2_recrutements_commentaires '
		.'LEFT JOIN zcov2_utilisateurs u1 '
			.'ON u1.utilisateur_id = commentaire_utilisateur_id '
		.'LEFT JOIN zcov2_recrutements_candidatures '
			.'ON candidature_id = commentaire_candidature_id '
                .'LEFT JOIN zcov2_utilisateurs u2 '
                        .'ON u2.utilisateur_id = candidature_id_utilisateur '		
		.'LEFT JOIN zcov2_recrutements '
			.'ON recrutement_id = candidature_id_recrutement '
		.'WHERE commentaire_id = :id');
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$retour = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $retour;
}

function SupprimerCommentaireShoutbox($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("DELETE FROM zcov2_recrutements_commentaires WHERE commentaire_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function EditerCommentaireShoutbox($id, $texte)
{
	$db = Doctrine_Manager::connection()->getDbh();
	$stmt = $db->prepare('UPDATE zcov2_recrutements_commentaires '
		.'SET commentaire_texte = :texte '
		.'WHERE commentaire_id = :id');
	$stmt->bindParam('id', $id);
	$stmt->bindParam('texte', $texte);
	return $stmt->execute();
}

