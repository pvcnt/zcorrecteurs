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
 * Modèle concernant les alertes des MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @begin 27/09/2008
 * @last 25/04/2009 vincent1870
 */

function CompterAlertes($statut)
{
	//Toutes = -1
	//Non-résolues = 0
	//Résolues = 1

	$add = '';
	if($statut > -1)
	{
		$add = ' WHERE mp_alerte_resolu = '.$statut;
	}

	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(mp_alerte_id) FROM zcov2_mp_alertes".$add);
	$stmt->execute();
	return $stmt->fetchColumn();
}

function VerifierAlerteDejaPostee()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On recherche si des alertes non-résolues existent pour ce MP
	$stmt = $dbh->prepare("SELECT mp_alerte_id FROM zcov2_mp_alertes
	WHERE mp_alerte_mp_id = :mp_id AND mp_alerte_resolu = 0");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->execute();
	$resultat = $stmt->fetchColumn();
	if(!empty($resultat) AND is_numeric($resultat))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function AjouterAlerte()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On ajoute l'alerte
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_alertes (mp_alerte_mp_id, mp_alerte_auteur, mp_alerte_date, mp_alerte_raison, mp_alerte_ip)
	VALUES (:mp_id, :auteur, NOW(), :raison, :ip)");
	$stmt->bindParam(':mp_id', $_GET['id']);
	$stmt->bindParam(':auteur', $_SESSION['id']);
	$stmt->bindValue(':raison', htmlspecialchars($_POST['texte']));
	$stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
	$stmt->execute();
	return true;
}

function ResoudreAlertes()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On résout toutes les alertes
	$stmt = $dbh->prepare("UPDATE zcov2_mp_alertes SET mp_alerte_resolu = 1, mp_alerte_modo = :user_id WHERE mp_alerte_resolu = 0");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->execute();
	return true;
}

//Liste des alertes sur un sujet
function ListerAlertes($debut, $nb)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(isset($_GET['solved']) && in_array($_GET['solved'], array(0, 1)))
	{
		$where = '= '.$_GET['solved'];
	}
	else
	{
		$where = 'IN(0, 1)';
	}

	$stmt = $dbh->prepare("
	SELECT mp_alerte_id, mp_alerte_raison, mp_alerte_ip, mp_alerte_resolu, mp_alerte_modo, 
	B.groupe_nom as groupe_nom, B.groupe_class as groupe_class, B.groupe_logo as groupe_logo, 
	mp_alerte_date, mp_titre, mp_id, mp_ferme, A.utilisateur_id as utilisateur_id, A.utilisateur_citation, 
	COALESCE(A.utilisateur_pseudo, 'Anonyme') AS utilisateur_pseudo, A.utilisateur_pourcentage as utilisateur_pourcentage,
	A.utilisateur_nb_sanctions as utilisateur_nb_sanctions, A.utilisateur_avatar as utilisateur_avatar, 
	A.utilisateur_signature as utilisateur_signature, C.utilisateur_id as modo_id, 
	IFNULL(C.utilisateur_pseudo, 'Inconnu') as modo_pseudo, D.groupe_class as modo_groupe_class
	FROM zcov2_mp_alertes
	LEFT JOIN zcov2_utilisateurs A ON zcov2_mp_alertes.mp_alerte_auteur = A.utilisateur_id
	LEFT JOIN zcov2_utilisateurs C ON zcov2_mp_alertes.mp_alerte_modo = C.utilisateur_id
	LEFT JOIN zcov2_groupes B ON A.utilisateur_id_groupe = B.groupe_id
	LEFT JOIN zcov2_groupes D ON C.utilisateur_id_groupe = D.groupe_id
	LEFT JOIN zcov2_mp_mp ON mp_alerte_mp_id = mp_id
	WHERE mp_alerte_resolu ".$where."
	ORDER BY mp_alerte_resolu ASC, mp_alerte_date DESC
	LIMIT ".$debut.", ".$nb);

	$stmt->execute();
	return $stmt->fetchAll();
}

//Marque une alerte comme résolue
function AlerteResolue($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_mp_alertes SET mp_alerte_resolu = 1, mp_alerte_modo = :user_id WHERE mp_alerte_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':user_id', $_SESSION['id']);

	$stmt->execute();
}

//Marque une alerte comme non résolue
/*
function AlerteNonResolue($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_mp_alertes SET mp_alerte_resolu = 0 WHERE mp_alerte_id = :id");
	$stmt->bindParam(':id', $id);

	$stmt->execute();
}
*/

function TrouverLaPageDeCetteAlerte($Alerte, $NbAlertes)
{
	//Dès qu'il y a un paramètre d'alerte dans l'URL, cette fonction est
	//appelée pour trouver sur quelle page se trouve l'alerte concernée.

	if(isset($_GET['solved']) && in_array($_GET['solved'], array(0, 1)))
	{
		$where = '= '.$_GET['solved'];
	}
	else
	{
		$where = 'IN(0, 1)';
	}

	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT mp_alerte_id
	FROM zcov2_mp_alertes
	WHERE mp_alerte_resolu ".$where."
	ORDER BY mp_alerte_date DESC");

	$stmt->execute();
	$resultat = $stmt->fetchAll();

	//On calcule la position de l'alerte
	$arreter_boucle = false;
	$i = 1;
	foreach($resultat as $clef => $valeur)
	{
		if(!$arreter_boucle)
		{
			if($valeur["mp_alerte_id"] != $Alerte)
			{
				$i++;
			}
			else
			{
				$arreter_boucle = true;
			}
		}
	}
	$nbAlertesParPage = 20;
	$NombreDePages = ceil($NbAlertes / 20);
	$PageCibleInversee = ceil($i/20);
	$PageCible = $NombreDePages - ($PageCibleInversee - 1);
	
	return $PageCible;
}