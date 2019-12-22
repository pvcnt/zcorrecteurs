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

function InfosCandidature($id1, $id2 = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if(is_null($id2))
		$where = 'candidature_id = :id';
	else
		$where = 'candidature_id_utilisateur = :id_membre AND candidature_id_recrutement = :id_recrutement';

	$stmt = $dbh->prepare("SELECT candidature_id, candidature_texte, candidature_pseudo,
		candidature_redaction, candidature_etat, candidature_correction_original,
		candidature_correction_corrige, candidature_correction_note, candidature_ip,
		candidature_commentaire, candidature_date_fin_correction, candidature_date_correction,
		candidature_date_debut_correction, candidature_test_type, candidature_test_tuto, candidature_test_texte, 
		candidature_date, candidature_date_debut_correction, candidature_date_fin_correction,
		candidature_date_correction, candidature_date_reponse, candidature_correcteur,
		candidature_correcteur_note, candidature_quiz_score, candidature_quiz_debut, candidature_quiz_fin,
		u1.utilisateur_id, u1.utilisateur_pseudo, u1.utilisateur_email,
		recrutement_id, recrutement_nom, recrutement_id_groupe, recrutement_redaction,
		recrutement_date_fin_epreuve, recrutement_etat, u3.utilisateur_id AS id_correcteur,
		u3.utilisateur_pseudo AS pseudo_correcteur,
		g1.groupe_nom, g1.groupe_class, g2.groupe_class AS groupe_admin,
		u2.utilisateur_id AS id_admin, u2.utilisateur_pseudo AS pseudo_admin,
		CASE WHEN candidature_date_fin_correction >= NOW() THEN 1
		ELSE 0
		END AS correction_possible
		FROM zcov2_recrutements_candidatures
		LEFT JOIN zcov2_recrutements ON candidature_id_recrutement = recrutement_id
		LEFT JOIN zcov2_utilisateurs u1 ON candidature_id_utilisateur = u1.utilisateur_id
		LEFT JOIN zcov2_utilisateurs u2 ON candidature_id_admin = u2.utilisateur_id
		LEFT JOIN zcov2_utilisateurs u3 ON candidature_correcteur = u3.utilisateur_id
		LEFT JOIN zcov2_groupes g1 ON u1.utilisateur_id_groupe = g1.groupe_id
		LEFT JOIN zcov2_groupes g2 ON u2.utilisateur_id_groupe = g2.groupe_id
		WHERE ".$where);
	if(is_null($id2))
		$stmt->bindParam(':id', $id1);
	else
	{
		$stmt->bindParam(':id_membre', $id1);
		$stmt->bindParam(':id_recrutement', $id2);
	}
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function ListerCandidatures($id, $orderby = 'etat')
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if ($orderby == 'note')
		$orderby = 'candidature_correcteur_note IS NOT NULL DESC, candidature_correcteur_note DESC';
	elseif ($orderby == 'score')
		$orderby = 'candidature_quiz_score IS NOT NULL DESC, candidature_quiz_score DESC';
	elseif ($orderby == 'id')
		$orderby = 'candidature_id ASC';
	elseif ($orderby == 'avis')
		$orderby = 'nb_oui DESC, nb_non ASC';
	else
		$orderby = 'candidature_etat ASC';

	$stmt = $dbh->prepare("SELECT candidature_id, candidature_etat, candidature_ip,
		candidature_date_fin_correction, candidature_date, candidature_date_correction,
		candidature_correcteur, candidature_pseudo, candidature_date_debut_correction,
		candidature_correcteur_note, lunonlu_utilisateur_id, lunonlu_commentaire_id, lunonlu_participe,
		candidature_date_fin_correction, u.utilisateur_id, u.utilisateur_pseudo,
		candidature_quiz_score,
		g.groupe_nom, g.groupe_class, u2.utilisateur_id AS id_correcteur,
		u2.utilisateur_pseudo AS pseudo_correcteur, g2.groupe_nom AS groupe_correcteur,
		g2.groupe_class AS groupe_class_correcteur,
		CASE WHEN candidature_date_fin_correction >= NOW()
			THEN 1
			ELSE 0
		END AS correction_possible,
		(
			SELECT COUNT(*) AS nb_avis
			FROM zcov2_recrutements_avis a
			WHERE a.candidature_id = c.candidature_id AND a.type = 0
		) AS nb_oui,
		(
			SELECT COUNT(*) AS nb_avis
			FROM zcov2_recrutements_avis a
			WHERE a.candidature_id = c.candidature_id AND a.type = 1
		) AS nb_non,
		(
			SELECT COUNT(*) AS nb_avis
			FROM zcov2_recrutements_avis a
			WHERE a.candidature_id = c.candidature_id AND a.type = 2
		) AS nb_reserve,
		(
			SELECT a.type
			FROM zcov2_recrutements_avis a
			WHERE a.candidature_id = c.candidature_id
			AND a.utilisateur_id = :mon_id
		) AS mon_avis
		FROM zcov2_recrutements_candidatures c
		LEFT JOIN zcov2_recrutements_lunonlu ON lunonlu_candidature_id = candidature_id AND lunonlu_utilisateur_id = :mon_id
		LEFT JOIN zcov2_utilisateurs u ON candidature_id_utilisateur = u.utilisateur_id
		LEFT JOIN zcov2_utilisateurs u2 ON candidature_correcteur = u2.utilisateur_id
		LEFT JOIN zcov2_groupes g ON u.utilisateur_id_groupe = g.groupe_id
		LEFT JOIN zcov2_groupes g2 ON u2.utilisateur_id_groupe = g2.groupe_id
		WHERE candidature_id_recrutement = :id AND candidature_etat <> ".CANDIDATURE_REDACTION."
		ORDER BY ".$orderby.", candidature_date");
	$stmt->bindParam('id', $id);
	$stmt->bindParam('mon_id', $_SESSION['id']);
	$stmt->execute();
	$liste = $stmt->fetchAll();

	// Calcul des clés blog_nb_commentaires et dernier_commentaire séparément
	$liste_id = array();
	foreach($liste as $cle => $valeur)
	{
		if (!in_array($valeur['candidature_id'], $liste_id))
			$liste_id[] = $valeur['candidature_id'];
	}
	if (empty($liste_id))
		return array();

	$stmt = $dbh->prepare("SELECT candidature_id,
		(SELECT COUNT(*)
		FROM zcov2_recrutements_commentaires
		WHERE commentaire_candidature_id = candidature_id)
		AS candidature_nb_commentaires,
		(SELECT MAX(commentaire_id)
		FROM zcov2_recrutements_commentaires
		WHERE commentaire_candidature_id = candidature_id)
		AS dernier_commentaire
		FROM zcov2_recrutements_candidatures
		WHERE candidature_id IN (".implode(',', $liste_id).")");
	$stmt->execute();
	$fetchAll = $stmt->fetchAll();
	$agregate = array();

	foreach($fetchAll as $f)
	{
		$agregate[$f['candidature_id']] = $f;
	}
	foreach($liste as &$candidature)
	{
		$candidature['candidature_nb_commentaires'] = $agregate[$candidature['candidature_id']]['candidature_nb_commentaires'];
		$candidature['dernier_commentaire'] = $agregate[$candidature['candidature_id']]['dernier_commentaire'];
		$candidature['image'] = array();

		//Si on a déjà lu au moins un commentaire
		if(!empty($candidature['lunonlu_utilisateur_id']))
		{
			//On a jamais posté de commentaire
			if(!$candidature['lunonlu_participe'])
			{
				//Il n'y a pas de nouveau commentaire depuis la dernière visite
				if($candidature['dernier_commentaire'] == $candidature['lunonlu_commentaire_id'])
				{
					$candidature['image']['nom'] = 'lightbulb_off';
					$candidature['image']['title'] = 'Pas de nouvelles réponses, jamais participé';
				}
				//Il y a des nouveaux commentaires depuis la dernière visiste
				else
				{
					$candidature['image']['nom'] = 'lightbulb';
					$candidature['image']['title'] = 'Nouvelles réponses, jamais participé';
				}
			}
			//On a déjà participé aux commentaires de cette candidature
			else
			{
				//Il n'y a pas de nouveau commentaire depuis la dernière visite
				if($candidature['dernier_commentaire'] == $candidature['lunonlu_commentaire_id'])
				{
					$candidature['image']['nom'] = 'lightbulb_off_add';
					$candidature['image']['title'] = 'Pas de nouvelles réponses, participé';
				}
				//Il y a des nouveaux commentaires depuis la dernière visite
				else
				{
					$candidature['image']['nom'] = 'lightbulb_add';
					$candidature['image']['title'] = 'Nouvelles réponses, participé';
				}
			}
		}
		//On a jamais lu de commentaire sur cette candidature
		else
		{
			$candidature['image']['nom'] = 'lightbulb';
			$candidature['image']['title'] = 'Nouvelles réponses, jamais participé';
		}
	}

	return $liste;
}

function AjouterCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_recrutements_candidatures(candidature_id_utilisateur,
		candidature_id_recrutement, candidature_date, candidature_texte, candidature_pseudo,
		candidature_redaction, candidature_etat, candidature_ip)
		VALUES(:id_utilisateur, :id_recrutement, NOW(), :texte, :pseudo, :redaction,
		".CANDIDATURE_REDACTION.", :ip)");
	$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
	$stmt->bindParam(':id_recrutement', $id);
	$stmt->bindParam(':pseudo', $_SESSION['pseudo']);
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindValue(':redaction', isset($_POST['redaction']) ? $_POST['redaction'] : '');
	$stmt->bindValue(':ip', 0);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements');
}

function EditerMotivationCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_texte = :texte, candidature_redaction = :redaction,
		candidature_date = NOW()
		WHERE candidature_id = :id");
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindValue(':redaction', isset($_POST['redaction']) ? $_POST['redaction'] : '');
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements');
}

function EditerQuestionnaireCandidature($id, $score)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_quiz_score = :score,
		candidature_date = NOW()
		WHERE candidature_id = :id");
	$stmt->bindParam(':score', $score);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function EnvoyerMotivationCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_etat = ".CANDIDATURE_ENVOYE.",
		candidature_date = NOW()
		WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements');
}

function EditerCorrectionCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_correction_corrige = :texte, candidature_date_correction = NOW(),
		candidature_correction_note = :note
		WHERE candidature_id = :id");
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':note', $_POST['note_correction']);
	$stmt->execute();
}

function EditerCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$add = isset($_POST['texte']) ? ', candidature_correction_original = :texte' : '';
	$add .= isset($_POST['date_fin']) ? ', candidature_date_fin_correction = :date_fin' : '';

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_texte = :motiv, candidature_redaction = :redaction,
		candidature_etat = :etat".$add."
		WHERE candidature_id = :id");
	$stmt->bindParam(':motiv', $_POST['motiv']);
	$stmt->bindValue(':redaction', isset($_POST['redaction']) ? $_POST['redaction'] : '');
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':etat', $_POST['statut'], PDO::PARAM_INT);
	if(isset($_POST['texte']))
		$stmt->bindParam(':texte', $_POST['texte']);
	if(isset($_POST['date_fin']))
		$stmt->bindParam(':date_fin', $_POST['date_fin']);
	$stmt->execute();
}

function EnvoyerCorrectionCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	UPDATE zcov2_recrutements_candidatures
	SET candidature_etat = ".CANDIDATURE_TESTE.", candidature_date_correction = NOW()
	WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function AccepterRefuserCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$etat = (int)$_POST['etat'];

	$stmt = $dbh->prepare("
	UPDATE zcov2_recrutements_candidatures
	SET candidature_etat = :etat, candidature_commentaire = :texte, candidature_id_admin = :u, candidature_date_reponse = NOW()
	WHERE candidature_id = :id");
	$stmt->bindParam(':texte', $_POST['comm']);
	$stmt->bindParam(':etat', $etat);
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':u', $_SESSION['id']);
	$stmt->execute();
}

function TesterCandidature($id, $nom_fichier)
{
	$nom_fichier2 = '';
	$dbh = Doctrine_Manager::connection()->getDbh();
	if (!empty($_POST['texte']))
	{
		$type = TEST_TEXTE;
	}
	elseif (!empty($nom_fichier))
	{
		$type = TEST_TUTO;
	}
	else
	{
		$type = TEST_DEFAUT;
		$nom_fichier = '0101010101.tuto';
		$nom_fichier2 = '0101010101.txt';
	}
	
	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures " .
			"SET candidature_etat = ".CANDIDATURE_ATTENTE_TEST.", candidature_correction_original = :texte, ".
			"candidature_correction_corrige = :texte, candidature_date_debut_correction = NOW(), ".
			"candidature_date_fin_correction = :date_fin, candidature_test_type = :type, ".
			"candidature_test_tuto = :tuto, candidature_test_texte = :txt " .
			"WHERE candidature_id = :id");
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindParam(':date_fin', $_POST['date_fin']);
	$stmt->bindParam(':tuto', $nom_fichier);
	$stmt->bindParam(':txt', $nom_fichier2);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function SupprimerCandidature($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_recrutements_candidatures
		WHERE candidature_id=:id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements');
}

/**
*	Indiquer le désistement d'une candidature
*	@param integer $id		ID de la candidature
*	@return void
**/
function DesisterCandidature($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	UPDATE zcov2_recrutements_candidatures
		SET candidature_etat = ".CANDIDATURE_DESISTE."
	WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
*	Devenir correcteur de la copie d'une candidature
*	@param integer $id		ID de la candidature
*	@return void
**/
function DevenirCorrecteurCandidature($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	UPDATE zcov2_recrutements_candidatures
		SET candidature_correcteur = ".$_SESSION['id']."
	WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
 * Retire le correcteur d'une copie.
 * @param integer $id		ID de la candidature.
 */
function SupprimerCorrecteurCandidature($id)
{
	$db = Doctrine_Manager::connection()->getDbh();

	$stmt = $db->prepare("
	UPDATE zcov2_recrutements_candidatures
		SET candidature_correcteur = null
	WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function NoterCopie($id, $note)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$note = str_replace(',', '.', $note);

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements_candidatures
		SET candidature_correcteur_note = :note
		WHERE candidature_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':note', $note);
	$stmt->execute();
}

function RecupererIdCandidatureSuivante($id_candidature, $id_recrutement)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("SELECT candidature_id
		FROM zcov2_recrutements_candidatures
		WHERE candidature_id > :id_cand AND candidature_id_recrutement = :id_recrut AND candidature_etat <> ".CANDIDATURE_REDACTION."
		ORDER BY candidature_id
		LIMIT 1");
	$stmt->bindParam(':id_cand', $id_candidature);
	$stmt->bindParam(':id_recrut', $id_recrutement);
	$stmt->execute();
	return $stmt->fetchColumn();
}

function RecupererIdCandidaturePrecedente($id_candidature, $id_recrutement)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("SELECT candidature_id
		FROM zcov2_recrutements_candidatures
		WHERE candidature_id < :id_cand AND candidature_id_recrutement = :id_recrut AND candidature_etat <> ".CANDIDATURE_REDACTION."
		ORDER BY candidature_id
		LIMIT 1");
	$stmt->bindParam(':id_cand', $id_candidature);
	$stmt->bindParam(':id_recrut', $id_recrutement);
	$stmt->execute();
	return $stmt->fetchColumn();
}
