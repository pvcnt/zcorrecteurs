<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

function AjouterRecrutement($id_u, $id_grp, $nom, $texte, $lien, $date_fin_depot,
	$date_fin_epreuve, $etat, $prive, $nb_personnes, $redaction, $id_quiz)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_recrutements(recrutement_id_utilisateur,
		recrutement_id_groupe, recrutement_nom, recrutement_date,
		recrutement_date_fin_depot, recrutement_date_fin_epreuve, recrutement_etat,
		recrutement_texte, recrutement_prive, recrutement_nb_personnes,
		recrutement_redaction, recrutement_lien, recrutement_id_quiz)
		VALUES(:id_utilisateur, :id_groupe, :nom, NOW(), :date_fin_depot,
		:date_fin_epreuve, :etat, :texte, :prive, :nb_personnes, :redaction, :lien, :id_quiz)");
	$stmt->bindParam(':id_utilisateur', $id_u);
	$stmt->bindParam(':id_groupe', $id_grp);
	$stmt->bindParam(':nom', $nom);
	$stmt->bindParam(':date_fin_depot', $date_fin_depot);
	$stmt->bindParam(':date_fin_epreuve', $date_fin_epreuve);
	$stmt->bindParam(':etat', $etat);
	$stmt->bindParam(':texte', $texte);
	$stmt->bindValue(':prive', (int)$prive);
	$stmt->bindParam(':nb_personnes', $nb_personnes);
	$stmt->bindValue(':redaction', (int)$redaction);
	$stmt->bindValue(':lien', $lien);
	$stmt->bindValue(':id_quiz', $id_quiz);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements_prives');
	Container::getService('zco_core.cache')->Delete('liste_recrutements_publics');
}

function EditerRecrutement($id, $params)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$set = array();
	$bind = array();

	foreach($params as $cle => $valeur)
	{
		if(is_null($valeur))
			$set[] = 'recrutement_'.$cle.' = NULL';
		elseif(is_bool($valeur))
			$set[] = 'recrutement_'.$cle.' = '.(int)$valeur;
		elseif($valeur == 'NOW')
			$set[] = 'recrutement_'.$cle.' = NOW()';
		else
		{
			$set[] = 'recrutement_'.$cle.' = :'.$cle;
			$bind[$cle] = $valeur;
		}
	}

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements
		SET ".implode(', ', $set)."
		WHERE recrutement_id = :id");
	$stmt->bindParam(':id', $id);
	foreach($bind as $cle => &$valeur)
		$stmt->bindParam(':'.$cle, $valeur);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_recrutements_prives');
	Container::getService('zco_core.cache')->Delete('liste_recrutements_publics');
}

function SupprimerRecrutement($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_recrutements WHERE recrutement_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();


	$stmt = $dbh->prepare("DELETE FROM zcov2_recrutements_candidatures WHERE candidature_id_recrutement = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();


	Container::getService('zco_core.cache')->Delete('liste_recrutements_prives');
	Container::getService('zco_core.cache')->Delete('liste_recrutements_publics');
}

function ListerRecrutements()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$add = !empty($_GET['id']) && in_array($_GET['id'], array(RECRUTEMENT_CACHE, RECRUTEMENT_FINI, RECRUTEMENT_OUVERT, RECRUTEMENT_TEST)) ? ' AND recrutement_etat = '.$_GET['id'] : '';

	if(verifier('recrutements_voir_prives')) $add .= '';
	else $add .= ' AND recrutement_prive = 0';

	$stmt = $dbh->prepare("
	SELECT recrutement_id, recrutement_id_utilisateur, recrutement_id_groupe, recrutement_nom, recrutement_etat, recrutement_texte, recrutement_prive,
	recrutement_date, recrutement_date_fin_depot, utilisateur_id, utilisateur_pseudo, recrutement_nb_personnes, groupe_id, groupe_nom,
	groupe_logo, groupe_class, candidature_etat, recrutement_lien,
	CASE
		WHEN recrutement_date_fin_depot IS NULL THEN 1
		WHEN recrutement_date_fin_depot >= NOW() THEN 1
		ELSE 0
	END AS depot_possible
	FROM zcov2_recrutements
	LEFT JOIN zcov2_utilisateurs ON recrutement_id_utilisateur = utilisateur_id
	LEFT JOIN zcov2_groupes ON recrutement_id_groupe = groupe_id
	LEFT JOIN zcov2_recrutements_candidatures ON candidature_id_recrutement = recrutement_id AND candidature_id_utilisateur = :id
	WHERE recrutement_etat <> ".RECRUTEMENT_CACHE.$add."
	ORDER BY recrutement_etat, recrutement_date DESC");
	$stmt->bindParam(':id', $_SESSION['id']);
	$stmt->execute();

	return $stmt->fetchAll();
}

function ListerRecrutementsAdmin()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$add = !empty($_GET['id']) && in_array($_GET['id'], array(RECRUTEMENT_CACHE, RECRUTEMENT_FINI, RECRUTEMENT_OUVERT, RECRUTEMENT_TEST)) ? 'WHERE recrutement_etat = '.$_GET['id'] : '';

	$stmt = $dbh->prepare("
	SELECT recrutement_id, recrutement_id_utilisateur, recrutement_id_groupe, recrutement_nom, recrutement_etat, recrutement_texte, recrutement_prive,
	recrutement_nb_personnes, recrutement_date, recrutement_date_fin_depot,	utilisateur_id, utilisateur_pseudo, groupe_id, groupe_nom, groupe_logo,
	groupe_class,
	(SELECT COUNT(*) FROM zcov2_recrutements_candidatures WHERE candidature_id_recrutement = recrutement_id AND candidature_etat <> ".CANDIDATURE_REDACTION.") AS nb_candidatures
	FROM zcov2_recrutements
	LEFT JOIN zcov2_utilisateurs ON recrutement_id_utilisateur = utilisateur_id
	LEFT JOIN zcov2_groupes ON recrutement_id_groupe = groupe_id
	".$add."
	ORDER BY recrutement_etat, recrutement_date DESC");

	$stmt->execute();

	return $stmt->fetchAll();
}

function InfosRecrutement($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT recrutement_id, recrutement_id_utilisateur,
		recrutement_id_groupe, recrutement_nom, recrutement_etat, recrutement_texte,
		recrutement_prive, recrutement_redaction, recrutement_nb_personnes,
		recrutement_date, recrutement_nb_lus, recrutement_lien, recrutement_id_quiz,
		recrutement_date_fin_depot, utilisateur_id, utilisateur_pseudo,
		g1.groupe_id, g1.groupe_nom, g1.groupe_logo, g1.groupe_class,
		g2.groupe_nom as groupe_nom_admin, g2.groupe_class AS groupe_class_admin,
		(SELECT COUNT(*) FROM zcov2_recrutements_candidatures
			WHERE candidature_id_recrutement = recrutement_id
			AND candidature_etat <> ".CANDIDATURE_REDACTION.") AS nb_candidatures,
		CASE
			WHEN recrutement_date_fin_depot IS NULL THEN 1
			WHEN recrutement_date_fin_depot >= NOW() THEN 1
			ELSE 0
		END AS depot_possible
		FROM zcov2_recrutements
		LEFT JOIN zcov2_utilisateurs ON recrutement_id_utilisateur = utilisateur_id
		LEFT JOIN zcov2_groupes g1 ON recrutement_id_groupe = g1.groupe_id
		LEFT JOIN zcov2_groupes g2 ON utilisateur_id_groupe = g2.groupe_id
		WHERE recrutement_id = :id
		ORDER BY recrutement_date DESC");
	$stmt->bindParam(':id', $id);

	$stmt->execute();

	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function IncrementerNombreLus($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_recrutements " .
			"SET recrutement_nb_lus = recrutement_nb_lus + 1 " .
			"WHERE recrutement_id = :id");
	$stmt->bindParam(':id', $id);

	$stmt->execute();

}

function ListerRecrutementsSitemap()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("SELECT recrutement_nom, recrutement_id " .
			"FROM zcov2_recrutements " .
			"WHERE recrutement_etat <> ".RECRUTEMENT_CACHE." AND recrutement_prive = 0 " .
			"ORDER BY recrutement_etat, recrutement_date DESC");
	$stmt->execute();


	return $stmt->fetchAll();
}
