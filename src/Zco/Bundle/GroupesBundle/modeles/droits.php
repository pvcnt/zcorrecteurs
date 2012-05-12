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
 *Modèle gérant les actions sur les droits.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 17/11/2008
 * @last 17/11/2008 vincent1870
 */

function AjouterDroit($nom, $desc, $desc_longue, $id_cat, $choix_cat, $choix_binaire)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_droits(droit_id_categorie, " .
			"droit_nom, droit_description, droit_choix_categorie, " .
			"droit_choix_binaire, droit_description_longue) " .
			"VALUES(:cat, :nom, :desc, :choix_cat, :choix_binaire, :desc_longue)");
	$stmt->bindParam(':nom', $nom);
	$stmt->bindParam(':desc', $desc);
	$stmt->bindParam(':desc_longue', $desc_longue);
	$stmt->bindParam(':cat', $id_cat);
	$stmt->bindParam(':choix_cat', $choix_cat);
	$stmt->bindParam(':choix_binaire', $choix_binaire);
	$stmt->execute();
	return $dbh->lastInsertId();

	/*$xml = new DomDocument();
	$xml->load(APP_PATH.'/config/droits.xml');
	$xml->formatOutput = true;
	$roots = $xml->getElementsByTagName('droits');
	$root = $roots->item(0);

	$droit = $xml->createElement('droit');
	$droit->setAttribute('idcat', $id_cat);
	$droit->setAttribute('parcategorie', $choix_cat);
	$droit->setAttribute('binaire', $choix_binaire);
	$droit = $root->appendChild($droit);

	$_nom = $xml->createElement('nom');
	$_nom = $droit->appendChild($_nom);
	$nom_text = $xml->createTextNode($nom);
	$nom_text = $_nom->appendChild($nom_text);

	$nom = $xml->createElement('description');
	$nom = $droit->appendChild($nom);
	$nom_text = $xml->createCDATASection($desc);
	$nom_text = $nom->appendChild($nom_text);

	$nom = $xml->createElement('commentaire');
	$nom = $droit->appendChild($nom);
	$nom_text = $xml->createCDATASection($desc_longue);
	$nom_text = $nom->appendChild($nom_text);

	$xml->save(APP_PATH.'/config/droits.xml');*/
}

function EditerDroit($infos, $nom, $desc, $desc_longue, $id_cat, $choix_cat, $choix_binaire)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Édition en BDD
	$stmt = $dbh->prepare("UPDATE zcov2_droits " .
			"SET droit_id_categorie = :cat, droit_nom = :nom, droit_description = :desc, " .
			"droit_choix_categorie = :choix_cat, droit_choix_binaire = :choix_binaire, " .
			"droit_description_longue = :desc_longue " .
			"WHERE droit_id = :id");
	$stmt->bindParam(':id', $infos['droit_id']);
	$stmt->bindValue(':cat', (int)$id_cat);
	$stmt->bindParam(':nom', $nom);
	$stmt->bindParam(':desc', $desc);
	$stmt->bindParam(':desc_longue', $desc_longue);
	$stmt->bindValue(':choix_cat', (int)$choix_cat);
	$stmt->bindValue(':choix_binaire', (int)$choix_binaire);
	$stmt->execute();

	//Si on change la catégorie parente
	if($infos['droit_id_categorie'] != (int)$id_cat && $choix_cat == false)
	{
		$stmt = $dbh->prepare("UPDATE zcov2_groupes_droits " .
				"SET gd_id_categorie = :id_cat " .
				"WHERE gd_id_droit = :id");
		$stmt->bindParam(':id', $infos['droit_id']);
		$stmt->bindValue(':id_cat', (int)$id_cat);
		$stmt->execute();
	}
	elseif($infos['droit_id_categorie'] != $id_cat && $choix_cat == true)
	{
		$stmt = $dbh->prepare("DELETE FROM zcov2_groupes_droits " .
				"WHERE gd_id_droit = :id AND gd_id_categorie <> :id_cat");
		$stmt->bindParam(':id', $infos['droit_id']);
		$stmt->bindValue(':id_cat', (int)$id_cat);
		$stmt->execute();
	}

	//Si on change le choix par catégorie
	if($infos['droit_choix_categorie'] == true && $choix_cat == false)
	{
		$stmt = $dbh->prepare("DELETE FROM zcov2_groupes_droits " .
				"WHERE gd_id_droit = :id AND gd_id_categorie <> :id_cat");
		$stmt->bindParam(':id', $infos['droit_id']);
		$stmt->bindValue(':id_cat', (int)$id_cat);
		$stmt->execute();
	}

	//Si on change le choix binaire
	if($infos['droit_choix_binaire'] != $choix_binaire)
	{
		$stmt = $dbh->prepare("DELETE FROM zcov2_groupes_droits " .
				"WHERE gd_id_droit = :id");
		$stmt->bindParam(':id', $infos['droit_id']);
		$stmt->execute();
	}
}

function SupprimerDroit($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_droits WHERE droit_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$stmt = $dbh->prepare("DELETE FROM zcov2_groupes_droits WHERE gd_id_droit = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

function ListerDroits()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT droit_id, droit_nom, droit_description, " .
			"droit_choix_categorie, droit_choix_binaire, " .
			"cat_id, cat_nom, cat_niveau " .
			"FROM zcov2_droits " .
			"LEFT JOIN zcov2_categories ON droit_id_categorie = cat_id " .
			"ORDER BY cat_gauche, droit_description");
	$stmt->execute();
	return $stmt->fetchAll();
}

function ListerDroitsCategorie($id, $choix_cat = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if(!is_null($choix_cat))
	{
		if($choix_cat == true)
			$add = ' AND droit_choix_categorie = 1 ';
		else
			$add = ' AND droit_choix_categorie = 0 ';
	}
	else
		$add = '';

	$stmt = $dbh->prepare("SELECT droit_id, droit_nom, droit_description, droit_choix_categorie, droit_choix_binaire " .
			"FROM zcov2_droits " .
			"LEFT JOIN zcov2_categories ON droit_id_categorie = cat_id " .
			"WHERE cat_id = :id ".$add .
			"ORDER BY droit_choix_categorie");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchAll();
}

function InfosDroit($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT droit_id, droit_id_categorie, droit_nom, " .
			"droit_description, droit_choix_categorie, droit_choix_binaire, " .
			"droit_description_longue, " .
			"cat_id, cat_nom, cat_gauche, cat_droite, cat_niveau  " .
			"FROM zcov2_droits " .
			"LEFT JOIN zcov2_categories ON droit_id_categorie = cat_id " .
			"WHERE droit_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function RecupererValeurDroit($droit, $groupe)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT gd_id_categorie, gd_valeur " .
			"FROM zcov2_groupes_droits " .
			"WHERE gd_id_droit = :droit AND gd_id_groupe = :groupe");
	$stmt->bindParam(':droit', $droit);
	$stmt->bindParam(':groupe', $groupe);
	$stmt->execute();
	return $stmt->fetchAll();
}

function RecupererDroitsGroupe($groupe)
{
	if(($retour = Container::getService('zco_core.cache')->Get('droits_groupe_'.$groupe)) === false)
	{
		//Récupération depuis la BDD
		$dbh = Doctrine_Manager::connection()->getDbh();
		$retour = array();

		$stmt = $dbh->prepare("SELECT gd_id_categorie, droit_id, droit_nom, droit_description, droit_choix_categorie, droit_choix_binaire, " .
				"gd_valeur " .
				"FROM zcov2_groupes_droits " .
				"LEFT JOIN zcov2_droits ON droit_id = gd_id_droit " .
				"WHERE gd_id_groupe = :groupe");
		$stmt->bindParam(':groupe', $groupe);
		$stmt->execute();
		$rows = $stmt->fetchAll();

		//Organisation de l'array sous la forme
		//array($nom_droit => $valeur, $nom_droit => array($id_cat1 => $valeur, $id_cat2 => $valeur))
		foreach($rows as $r)
		{
			if(!$r['droit_choix_categorie'])
				$retour[$r['droit_nom']] = (int)$r['gd_valeur'];
			else
				$retour[$r['droit_nom']][$r['gd_id_categorie']] = (int)$r['gd_valeur'];
			}

		Container::getService('zco_core.cache')->Set('droits_groupe_'.$groupe, $retour, 0);
	}
	return $retour;
}

function EditerDroitGroupe($groupe, $cat, $droit, $valeur)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_groupes_droits(gd_id_groupe, gd_id_droit, gd_id_categorie, gd_valeur) " .
			"VALUES(:groupe, :droit, :cat, :valeur) " .
			"ON DUPLICATE KEY UPDATE gd_valeur = :valeur");
	$stmt->bindParam(':groupe', $groupe);
	$stmt->bindParam(':cat', $cat);
	$stmt->bindParam(':droit', $droit);
	$stmt->bindParam(':valeur', $valeur);
	$stmt->execute();
}

function VerifierDroitsGroupe($groupe)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT gd_id_categorie, droit_id, droit_nom, " .
			"droit_description, droit_choix_categorie, droit_choix_binaire, " .
			"gd_valeur, COALESCE(c1.cat_id, c2.cat_id) AS cat_id, " .
			"COALESCE(c1.cat_nom, c2.cat_nom) AS cat_nom, " .
			"COALESCE(c1.cat_niveau, c2.cat_niveau) AS cat_niveau " .
			"FROM zcov2_droits " .
			"LEFT JOIN zcov2_groupes_droits ON droit_id = gd_id_droit AND gd_id_groupe = :groupe " .
			"LEFT JOIN zcov2_categories c1 ON gd_id_categorie = c1.cat_id " .
			"LEFT JOIN zcov2_categories c2 ON droit_id_categorie = c2.cat_id ".
			"ORDER BY COALESCE(c1.cat_gauche, c2.cat_gauche)");
	$stmt->bindParam(':groupe', $groupe);
	$stmt->execute();
	return $stmt->fetchAll();
}
