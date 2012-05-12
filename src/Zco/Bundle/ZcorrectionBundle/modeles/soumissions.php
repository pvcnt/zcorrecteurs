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

/**
 * Modèle pour la gestion des soumissions.
 *
 * @package zCorrecteurs.Fr
 * @author Savageman, vincent1870, DJ Fox
 * @begin 2007
 * @last 14/08/2008 vincent1870
 */

function parse_mini_tuto($chap, $encodage, $id_partie = 0) ######### À adapter à DOM
{
    $chap_id = $chap->getAttribute('id'); //Récupération de l'id du chapitre
    $enfants = $chap->childNodes; //On récupère les enfants du chapitre

    foreach($enfants as $enfant)
    {
        if($enfant->nodeName !== '#text')
        {
            ${$enfant->nodeName} = $enfant; //On récupère de cette façon les enfants.
        }
    }
    $titre = $titre->nodeValue;
    $avancement = $avancement->nodeValue;
    $difficulte = $difficulte->nodeValue;
    $introduction = $introduction->nodeValue;
    $conclusion = $conclusion->nodeValue;

	//Insertion du chapitre
	$id_mini_tuto = AjouterMiniTuto($id_partie, trim($titre), (int)$avancement, (int)$difficulte, trim($introduction), trim($conclusion), (string)$chap_id);

	//Ajout des sous-parties
    if(isset($sousparties) AND $sousparties->hasChildNodes())
    {
	    $sousparties = $sousparties->childNodes; //On récupère les enfants de "sousparties"

	    foreach($sousparties as $souspartie)
	    {
    	    if($souspartie->nodeName == 'souspartie')
	        {
    	        $souspartie_id = $souspartie->getAttribute('id'); //On récupère l'id de la sous-partie
	            $souspartie_enfants = $souspartie->childNodes; //On récupère les enfants de la sous-partie
	            foreach($souspartie_enfants as $souspartie_enfant)
                {
                    if($souspartie_enfant->nodeName !== '#text')
                    {
                        ${'souspartie_'.$souspartie_enfant->nodeName} = $souspartie_enfant;
                    }
                }
                $souspartie_titre = $souspartie_titre->nodeValue;
                $souspartie_texte = $souspartie_texte->nodeValue;

    		    AjouterSousPartie($id_mini_tuto, trim($souspartie_titre), trim($souspartie_texte), (string)$souspartie_id);
	        }
	    }
    }

	//QCM
	if(isset($qcm) AND $qcm->hasChildNodes())
	{
	    $qcm = $qcm->childNodes; //On récupère les enfants de "qcm"
    	//Ajout des questions
	    foreach($qcm as $question)
	    {
    	    if($question->nodeName == 'question')
	        {
    	        $question_id = $question->getAttribute('id'); //On récupère l'id de la question
	            $question_enfants = $question->childNodes; //On récupère les enfants de la question
	            foreach($question_enfants as $question_enfant)
                {
                    if($question_enfant->nodeName !== '#text')
                    {
                        ${'question_'.$question_enfant->nodeName} = $question_enfant;
                    }
                }
                $question_label = $question_label->nodeValue;
                $question_explication = $question_explication->nodeValue;

    		    $id_question = AjouterQuestion($id_mini_tuto, trim($question_label), trim($question_explication), (string) $question_id);

    		    //Ajout des réponses
                if(isset($question_reponses) AND $question_reponses->hasChildNodes())
                {
		            $reponses = $question_reponses->childNodes; //On récupère les enfants de "reponses"
		            foreach($reponses as $reponse)
		            {
    		            if($reponse->nodeName == 'reponse')
		                {
    		                $reponse_id = $reponse->getAttribute('id'); //On récupère l'id de la réponse
	    	                $reponse_vrai = $reponse->getAttribute('vrai'); //On récupère si la réponse est la bonne ou non
                            $reponse_texte = $reponse->nodeValue;

            			    AjouterReponse($id_question, trim($reponse_texte), (string) $reponse_vrai, (string) $reponse_id);
    		            }
		            }
	            }
	        }
	    }
    }
	return $id_mini_tuto;
}

function parse_big_tuto($tuto, $encodage)
{
    $tuto_id = $tuto->getAttribute('id'); //Récupération de l'id du tuto
    $enfants = $tuto->childNodes; //On récupère les enfants du tuto

    foreach($enfants as $enfant)
    {
        if($enfant->nodeName !== '#text')
        {
            ${$enfant->nodeName} = $enfant; //On récupère de cette façon les enfants.
        }
    }
    $titre = $titre->nodeValue;
    $avancement = $avancement->nodeValue;
    $difficulte = $difficulte->nodeValue;
    $introduction = $introduction->nodeValue;
    $conclusion = $conclusion->nodeValue;

    //On ajoute le big-tuto
	$id_big_tuto = AjouterBigTuto(trim($titre), (int)$avancement, (int)$difficulte, trim($introduction), trim($conclusion), (string) $tuto_id);

	//On récupère les enfants de "parties"
	if(isset($parties) AND $parties->hasChildNodes())
	{
	    $parties = $parties->childNodes;

	    foreach($parties as $partie)
	    {
    	    if($partie->nodeName == 'partie')
	        {
    	        $partie_id = $partie->getAttribute('id'); //On récupère l'id de la partie
	            $partie_enfants = $partie->childNodes; //On récupère les enfants de la partie
	            foreach($partie_enfants as $partie_enfant)
                {
                    if($partie_enfant->nodeName !== '#text')
                    {
                        ${'partie_'.$partie_enfant->nodeName} = $partie_enfant;
                    }
                }
                $partie_titre = $partie_titre->nodeValue;
                $partie_avancement = $partie_avancement->nodeValue;
                $partie_difficulte = $partie_difficulte->nodeValue;
                $partie_introduction = $partie_introduction->nodeValue;
                $partie_conclusion = $partie_conclusion->nodeValue;

    		    //On ajoute la partie
                $id_partie = AjouterPartie($id_big_tuto, trim($partie_titre), trim($partie_introduction), trim($partie_conclusion), (string) $partie_id);

                if(isset($partie_chapitres) AND $partie_chapitres->hasChildNodes())
                {
                    $chapitres = $partie_chapitres->childNodes; //On récupère les chapitres
		            foreach($chapitres as $chapitre)
		            {
        		        if($chapitre->nodeName == 'chapitre')
		                {
                			parse_mini_tuto($chapitre, $encodage, $id_partie); //On ajoute le chapitre
		                }
		            }
                }
	        }
	    }
    }
	return $id_big_tuto;
}

function SoumettreTuto($nom_fichier, $type, $tuto, $infos_valido, $msg_valido, $infos_mbr, $token, $encodage = 'UTF-8', $isNews = 0)
{
    $dbh = Doctrine_Manager::connection()->getDbh();

	if ($type == MINI_TUTO)
	{
		$id_tuto = parse_mini_tuto($tuto, $encodage);
	}
	else if ($type == BIG_TUTO)
	{
		$id_tuto = parse_big_tuto($tuto, $encodage);
	}
	else
	{
		return false;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_soumissions
	(soumission_id_utilisateur, soumission_pseudo_utilisateur, soumission_id_valido, soumission_pseudo_valido, soumission_description, soumission_sauvegarde, soumission_date, soumission_type_tuto, soumission_id_tuto, soumission_ip,
	soumission_prioritaire, soumission_news, soumission_token)
	VALUES
	(:id_utilisateur, :pseudo_utilisateur, :id_valido, :pseudo_valido, :description, :sauvegarde, NOW(), :type, :id_tuto, :ip, :prioritaire, :news, :token) ");

	$stmt->bindParam(':id_utilisateur', $infos_mbr['idsdz']);
	$stmt->bindParam(':pseudo_utilisateur', $infos_mbr['pseudo']);
	$stmt->bindParam(':id_valido', $infos_valido['idsdz']);
	$stmt->bindParam(':pseudo_valido', $infos_valido['pseudo']);
	$stmt->bindParam(':description',    $msg_valido);
	$stmt->bindParam(':sauvegarde',     $nom_fichier);
	$stmt->bindParam(':type',           $type);
	$stmt->bindParam(':id_tuto',        $id_tuto);
	$stmt->bindValue(':ip',             0);
	$stmt->bindParam(':prioritaire',    $isNews);
	$stmt->bindParam(':news',           $isNews);
	$stmt->bindParam(':token',           $token);

	if ($stmt->execute())
	{
	    $stmt->closeCursor();
	    return $dbh->lastInsertId();
	}
	else
	{
	    return false;
	}
}

function SoumissionAjouterCorrection($id_soumission, $id_correction)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
	SET soumission_id_correction_1 = :id_correction
	WHERE soumission_id = :id_soumission");

	$stmt->bindParam(':id_soumission', $id_soumission);
	$stmt->bindParam(':id_correction', $id_correction);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function SoumissionAjouterRecorrection($id_soumission, $id_recorrection)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
	SET soumission_id_correction_2 = :id_recorrection
	WHERE soumission_id = :id_soumission");

	$stmt->bindParam(':id_soumission',   $id_soumission);
	$stmt->bindParam(':id_recorrection', $id_recorrection);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function ListerSoumissions()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT DISTINCT
	soumission_id,
	soumission_id_utilisateur AS tutoteur_idsdz,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_id_valido AS valido_idsdz,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_description,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_recorrection,
	soumission_sauvegarde,
	soumission_commentaire,
	soumission_id_tuto,
	soumission_avancement,
	soumission_etat,
	'sdz' AS type,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	c1.correction_abandonee AS correction_abandonee,
	c1.correction_id_tuto_corrige AS correction_id_tuto_corrige_1,
	c2.correction_abandonee AS recorrection_abandonee,
	u3.utilisateur_pseudo AS pseudo_correcteur,
	u4.utilisateur_pseudo AS pseudo_recorrecteur,
	u3.utilisateur_id AS id_correcteur,
	u4.utilisateur_id AS id_recorrecteur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u3 ON c1.correction_id_correcteur = u3.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u4 ON c2.correction_id_correcteur = u4.utilisateur_id
	WHERE ((
		(soumission_id_correction_1 IS NULL)
		OR
		(soumission_id_correction_2 IS NULL AND c1.correction_date_fin IS NOT NULL)
		OR
		(c1.correction_abandonee = 1)
		OR
		(c2.correction_abandonee = 1)
	)
	AND
	(
		c1.correction_date_fin IS NULL
		OR
		(soumission_recorrection = 1 AND c2.correction_date_fin IS NULL)
	))
	AND soumission_etat != ".REFUSE."
	ORDER BY soumission_prioritaire DESC, soumission_recorrection DESC, s.soumission_date ASC");

	$retour = array();
	$stmt->execute();
	$retour = $stmt->fetchAll();
	
	include_once(__DIR__.'/drupal_support.php');
	try
	{
		$retour = TrierSoumissions($retour, ListerTicketsSupportDrupal(array('etat' => array(ENVOYE, RECORRECTION_DEMANDEE))));
	}
	catch (DrupalException $e)
	{
		$_SESSION['erreur'][] = $e->getMessage();
	}

	return $retour;
}

function CompterSoumissions() {
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT DISTINCT COUNT(*) AS nb
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	WHERE
	(
		(soumission_id_correction_1 IS NULL)
		OR
		(soumission_id_correction_2 IS NULL AND c1.correction_date_fin IS NOT NULL)
		OR
		(c1.correction_abandonee = 1)
		OR
		(c2.correction_abandonee = 1)
	)
	AND
	(
		c1.correction_date_fin IS NULL
		OR
		(
			soumission_recorrection = 1
			AND
			c2.correction_date_fin IS NULL
		)
	)  AND soumission_etat != ".REFUSE);

	$stmt->execute();
	$retour = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$retour = $retour['nb'];
		
	include_once(__DIR__.'/drupal_support.php');
	try
	{
		$retour += CompterTicketsSupportDrupal(array('etat' => array(ENVOYE, RECORRECTION_DEMANDEE)));
	}
	catch (DrupalException $e)
	{
		$_SESSION['erreur'][] = $e->getMessage();
	}
	
	return $retour;
}

function ListerSoumissionsCorrecteur() {
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT DISTINCT
	soumission_id,
	soumission_id_valido AS valido_idsdz,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_id_utilisateur AS tutoteur_idsdz,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_description,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_sauvegarde,
	soumission_recorrection,
	soumission_id_correction_1 AS id_correction,
	soumission_id_correction_2 AS id_recorrection,
	'sdz' AS type,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	u1.utilisateur_pseudo AS utilisateur_pseudo,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee,
	c1.correction_id_tuto_corrige AS id_tuto_corrige,
	c2.correction_id_tuto_corrige AS id_tuto_recorrige
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u1 ON soumission_id_utilisateur = u1.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u2 ON soumission_id_utilisateur = u2.utilisateur_id
	WHERE
	(c1.correction_id_correcteur = :id_correcteur AND soumission_recorrection = 0 AND c1.correction_abandonee = 0 AND c1.correction_date_fin IS NULL)
	OR
	(c2.correction_id_correcteur = :id_correcteur AND soumission_recorrection = 1 AND c2.correction_abandonee = 0 AND c2.correction_date_fin IS NULL)
	ORDER BY s.soumission_date ASC");

	$stmt->bindParam(':id_correcteur', $_SESSION['id']);

	$stmt->execute();
 	$retour = $stmt->fetchAll();

	include_once(__DIR__.'/drupal_support.php');
	try
	{
		$retour = TrierSoumissions($retour, ListerTicketsSupportDrupal(array('etat' => array(CORRECTION, RECORRECTION), 'assigne' => $_SESSION['pseudo'])));
	}
	catch (DrupalException $e)
	{
		$_SESSION['erreur'][] = $e->getMessage();
	}

	return $retour;
}

function TrierSoumissions(array $soumissions, array $tickets)
{
	$retour = array('prio' => array(), 'nonprio'=> array());
	
	foreach ($soumissions as $soumission)
	{
		if ($soumission['soumission_prioritaire'])
		{
			if (!isset($retour['prio'][strtotime($soumission['soumission_date'])]))
			{
				$retour['prio'][strtotime($soumission['soumission_date'])] = array();
			}
			
			$retour['prio'][strtotime($soumission['soumission_date'])][] = $soumission;
		}
		else
		{
			if (!isset($retour['nonprio'][strtotime($soumission['soumission_date'])]))
			{
				$retour['nonprio'][strtotime($soumission['soumission_date'])] = array();
			}
			
			$retour['nonprio'][strtotime($soumission['soumission_date'])][] = $soumission;
		}
	}
	
	foreach ($tickets as $ticket)
	{
		if ($ticket['priority'] >= 3)
		{
			if (!isset($retour['prio'][$ticket['created']]))
			{
				$retour['prio'][$ticket['created']] = array();
			}
			
			$retour['prio'][$ticket['created']][] = $ticket;
		}
		else
		{
			if (!isset($retour['nonprio'][$ticket['created']]))
			{
				$retour['nonprio'][$ticket['created']] = array();
			}
			
			$retour['nonprio'][$ticket['created']][] = $ticket;
		}
	}
	
	$retourOrdonne = array();
	ksort($retour['prio']);
	foreach ($retour['prio'] as $time => $tickets)
	{
		$retourOrdonne = array_merge($retourOrdonne, $tickets);
	}
	
	ksort($retour['nonprio']);
	foreach ($retour['nonprio'] as $time => $tickets)
	{
		$retourOrdonne = array_merge($retourOrdonne, $tickets);
	}
	
	return $retourOrdonne;
}

function CompterSoumissionsUtilisateur()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	WHERE ((soumission_recorrection = 0 AND c1.correction_date_fin IS NULL) OR (c1.correction_date_fin IS NOT NULL AND (soumission_recorrection = 1 AND c2.correction_date_fin IS NULL))) AND soumission_id_utilisateur = :id_utilisateur AND soumission_etat <> ".REFUSE);

	$stmt->bindParam(':id_utilisateur', $_SESSION['id']);

	if ($stmt->execute() && $retour = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		return $retour['nb'];
	}
	else
	{
		return false;
	}
}

function ListerSoumissionsAdmin()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT DISTINCT
	soumission_id,
	soumission_id_utilisateur AS tutoteur_idsdz,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_id_valido AS valido_idsdz,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_description,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_ip,
	soumission_recorrection,
	soumission_avancement,
	soumission_etat,
	soumission_sauvegarde,
	'sdz' AS type,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee,
	c1.correction_id_tuto_corrige AS id_tuto_corrige,
	c2.correction_id_tuto_corrige AS id_tuto_recorrige,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	u3.utilisateur_id AS id_correcteur,
	u3.utilisateur_pseudo AS pseudo_correcteur,
	u4.utilisateur_id AS id_recorrecteur,
	u4.utilisateur_pseudo AS pseudo_recorrecteur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u3 ON c1.correction_id_correcteur = u3.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u4 ON c2.correction_id_correcteur = u4.utilisateur_id
	WHERE ((((soumission_recorrection = 0 AND c1.correction_date_fin IS NULL) OR (c1.correction_date_fin IS NOT NULL AND soumission_recorrection = 1 AND c2.correction_date_fin IS NULL))) OR c1.correction_date_debut IS NULL) AND soumission_etat <> ".REFUSE."
	ORDER BY soumission_prioritaire DESC, soumission_recorrection DESC, s.soumission_date ASC");

	$stmt->execute();
 	$retour = $stmt->fetchAll();

	include_once(__DIR__.'/drupal_support.php');
	try
	{
		$retour = TrierSoumissions($retour, ListerTicketsSupportDrupal(array('etat' => array(ENVOYE, CORRECTION, RECORRECTION, RECORRECTION_DEMANDEE))));
	}
	catch (DrupalException $e)
	{
		$_SESSION['erreur'][] = $e->getMessage();
	}

	return $retour;
}

function SoumissionPrioritaire($id_soumission)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions SET soumission_prioritaire = 1 WHERE soumission_id = :id");
	$stmt->bindParam(':id', $id_soumission);

	if ($stmt->execute())
	{
		return true;
	}
	else
	{
		return false;
	}
}

function SoumissionNonPrioritaire($id_soumission)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions SET soumission_prioritaire = 0 WHERE soumission_id = :id");
	$stmt->bindParam(':id', $id_soumission);

	if ($stmt->execute())
	{
		return true;
	}
	else
	{
		return false;
	}
}

function ListerSoumissionsCorrigees($page, $etat, $id = null, $id2 = null, $pseudo = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$add = '';

	//Ajout de l'auteur
	if(!is_null($id2))
	{
		$add .= ' AND (soumission_id_utilisateur = :id';
		if(!is_null($pseudo))
			$add .= ' OR soumission_pseudo_utilisateur = :pseudo';
		$add .= ')';
	}

	//Ajout du correcteur / recorrecteur
	if(!is_null($id))
	{
		$add .= ' AND (c1.correction_id_correcteur = :id_zco OR c2.correction_id_correcteur = :id_zco)';
	}

	//Ajout de l'état
	if($etat == CORRECTION)
		$add .= ' AND ((soumission_recorrection = 0 AND c1.correction_id IS NOT NULL AND c1.correction_date_fin IS NULL) OR (soumission_recorrection = 1 AND c2.correction_date_fin IS NULL AND c2.correction_id IS NOT NULL))';
	elseif($etat == TERMINE_CORRIGE)
		$add .= ' AND ((soumission_recorrection = 0 AND c1.correction_date_fin IS NOT NULL) OR (soumission_recorrection = 1 AND c2.correction_date_fin IS NOT NULL))';
	else
		$add .= ' AND c1.correction_date_debut IS NOT NULL';

	$stmt = $dbh->prepare(
	"SELECT DISTINCT
	soumission_id,
	soumission_id_utilisateur AS tutoteur_idsdz,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_id_valido AS valido_idsdz,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_description,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_id_valido AS valido,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_sauvegarde,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_recorrection,
	soumission_etat,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_id_tuto_corrige AS id_tuto_corrige,
	c2.correction_id_tuto_corrige AS id_tuto_recorrige,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee,
	u2.utilisateur_pseudo AS soumission_tutoteur_pseudo_ancien_systeme,
	u3.utilisateur_id AS id_correcteur,
	u3.utilisateur_pseudo AS pseudo_correcteur,
	u4.utilisateur_id AS id_recorrecteur,
	u4.utilisateur_pseudo AS pseudo_recorrecteur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u2 ON soumission_id_utilisateur = u2.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u3 ON c1.correction_id_correcteur = u3.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u4 ON c2.correction_id_correcteur = u4.utilisateur_id
	".(!empty($_POST['nom']) ?
	'LEFT JOIN zcov2_push_mini_tutos mt1 ON c1.correction_id_tuto_corrige = mt1.mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt1 ON c1.correction_id_tuto_corrige = bt1.big_tuto_id
	LEFT JOIN zcov2_push_mini_tutos mt2 ON c2.correction_id_tuto_corrige = mt2.mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt2 ON c2.correction_id_tuto_corrige = bt2.big_tuto_id'
	: '')."
	WHERE soumission_etat != ".REFUSE.$add.
	(!empty($_POST['nom']) ?
	" AND (mt1.mini_tuto_titre LIKE '%".$_POST['nom']."%' || bt1.big_tuto_titre LIKE '%".$_POST['nom']."%' || mt2.mini_tuto_titre LIKE '%".$_POST['nom']."%' || bt2.big_tuto_titre LIKE '%".$_POST['nom']."%')"
	: '')."
	ORDER BY COALESCE(c2.correction_date_fin, c1.correction_date_fin) DESC
	LIMIT ".(($page - 1) * 30).", 30");

	if(!is_null($id2))
		$stmt->bindParam(':id', $id2);
	if(!is_null($pseudo))
		$stmt->bindParam(':pseudo', $pseudo);
	if(!is_null($id))
		$stmt->bindParam(':id_zco', $id);

	$stmt->execute();

	return $stmt->fetchAll();
}

function CompterSoumissionsCorrigees($etat, $id = null, $id2 = null, $pseudo = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$add = '';

	//Ajout de l'auteur
	if(!is_null($id2))
	{
		$add .= ' AND (soumission_id_utilisateur = :id';
		if(!is_null($pseudo))
			$add .= ' OR soumission_pseudo_utilisateur = :pseudo';
		$add .= ')';
	}

	//Ajout du correcteur / recorrecteur
	if(!is_null($id))
	{
		$add .= ' AND (c1.correction_id_correcteur = :id_zco OR c2.correction_id_correcteur = :id_zco)';
	}

	//Ajout de l'état
	if($etat == CORRECTION)
		$add .= ' AND ((soumission_recorrection = 0 AND c1.correction_id IS NOT NULL AND c1.correction_date_fin IS NULL) OR (soumission_recorrection = 1 AND c2.correction_date_fin IS NULL AND c2.correction_id IS NOT NULL))';
	elseif($etat == TERMINE_CORRIGE)
		$add .= ' AND ((soumission_recorrection = 0 AND c1.correction_date_fin IS NOT NULL) OR (soumission_recorrection = 1 AND c2.correction_date_fin IS NOT NULL))';
	else
		$add .= ' AND c1.correction_date_debut IS NOT NULL';

	$stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	".(!empty($_POST['nom']) ?
	'LEFT JOIN zcov2_push_mini_tutos mt1 ON c1.correction_id_tuto_corrige = mt1.mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt1 ON c1.correction_id_tuto_corrige = bt1.big_tuto_id
	LEFT JOIN zcov2_push_mini_tutos mt2 ON c2.correction_id_tuto_corrige = mt2.mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt2 ON c2.correction_id_tuto_corrige = bt2.big_tuto_id'
	: '')."
	WHERE soumission_etat != ".REFUSE.
	$add.
	(!empty($_POST['nom']) ?
	" AND (mt1.mini_tuto_titre LIKE '%".$_POST['nom']."%' || bt1.big_tuto_titre LIKE '%".$_POST['nom']."%' || mt2.mini_tuto_titre LIKE '%".$_POST['nom']."%' || bt2.big_tuto_titre LIKE '%".$_POST['nom']."%')"
	: ''));

	if(!is_null($id2))
		$stmt->bindParam(':id', $id2);
	if(!is_null($pseudo))
		$stmt->bindParam(':pseudo', $pseudo);
	if(!is_null($id))
		$stmt->bindParam(':id_zco', $id);

	$stmt->execute();

	return $stmt->fetchColumn();
}

function InfosSoumission($id_soumission)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	soumission_id,
	soumission_description,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_recorrection,
	soumission_avancement,
	soumission_sauvegarde,
	soumission_id_tuto,
	soumission_etat,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_correcteur_invisible AS correcteur_invisible,
	c2.correction_correcteur_invisible AS recorrecteur_invisible,
	c1.correction_id_tuto_corrige AS id_tuto_corrige,
	c2.correction_id_tuto_corrige AS id_tuto_recorrige,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	u1.utilisateur_pseudo AS utilisateur_pseudo,
	c1.correction_abandonee AS correction_abandonnee,
	c2.correction_abandonee AS recorrection_abandonnee,
	u3.utilisateur_id AS id_correcteur,
	u3.utilisateur_pseudo AS pseudo_correcteur,
	u4.utilisateur_pseudo AS pseudo_recorrecteur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u1 ON soumission_id_utilisateur = u1.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u2 ON soumission_id_utilisateur = u2.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u3 ON c1.correction_id_correcteur = u3.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u4 ON c2.correction_id_correcteur = u4.utilisateur_id
	WHERE soumission_id = :id_soumission");

	$stmt->bindParam(':id_soumission', $id_soumission);

	if($stmt->execute() && $resultat = $stmt->fetch(PDO::FETCH_ASSOC)){
		$stmt->closeCursor();
		return $resultat;
	}

	return false;
}

function SoumissionToken($tok)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$q = $dbh->prepare('SELECT soumission_id FROM zcov2_push_soumissions '
		.'WHERE soumission_token = ?');
	$q->execute(array($tok));
	return $q->fetchColumn();
}

function MettreAJourSoumission($soumission_id) {
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
	SET soumission_commentaire=:texte
	WHERE soumission_id=:soumission_id
	");

	$stmt->bindParam(':soumission_id', $soumission_id);
	$stmt->bindParam(':texte', $_POST['comm2']);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

//5 fonctions suivantes ajoutées par DJ Fox
function ReprendreDepuisZer0Correction($soumission_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On récupère les infos nécessaires sur la soumission
	$stmt = $dbh->prepare("SELECT soumission_id, soumission_id_tuto, soumission_id_correction_1, soumission_id_correction_2,
	soumission_type_tuto
	FROM zcov2_push_soumissions
	WHERE soumission_id = :soumission_id");
	$stmt->bindParam(':soumission_id', $soumission_id);
	$stmt->execute();


	$InfosSoumission = $stmt->fetch(PDO::FETCH_ASSOC);

	//Si la soumission existe
	if(!empty($InfosSoumission['soumission_id']))
	{
		//Si c'est un mini-tuto
		if($InfosSoumission['soumission_type_tuto'] == 1)
		{
			//Si le mini-tutoriel a été corrigé mais n'a pas été recorrigé
			if(!empty($InfosSoumission['soumission_id_correction_1']) AND empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du mini-tuto corrigé ainsi que les date de début et de fin de correction.
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige, correction_date_debut, correction_date_fin, correction_id_correcteur
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

				$stmt->execute();

				$InfosCorrection = $stmt->fetch(PDO::FETCH_ASSOC);

				if($InfosCorrection['correction_id_correcteur'] == $_SESSION['id'] || verifier('zcorrection_editer_tutos'))
				{
					if(!empty($InfosCorrection['correction_date_debut']) AND empty($InfosCorrection['correction_date_fin']))
					{
						//On supprime le mini-tuto corrigé
						SupprimerMiniTuto($InfosCorrection['correction_id_tuto_corrige']);

						//On copie le mini-tuto original pour réinitialiser la correction.
						$tuto_duplique = DupliquerMiniTuto($InfosSoumission['soumission_id_tuto']);

						//On met à jour la correction
						$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
						SET correction_id_tuto_corrige = :tuto_duplique, correction_date_debut = :vide, correction_date_fin = :vide,
						correction_commentaire = :vide, correction_correcteur_invisible = :un
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':tuto_duplique', $tuto_duplique);
						$stmt->bindValue(':vide', NULL, PDO::PARAM_NULL);
						$stmt->bindValue(':un', '1');
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

						$stmt->execute();

						return true;
					}
					else
					{
						return 'La correction n\'a pas été commencée ou elle est finie.';
					}
				}
				else
				{
					return 'Vous n\'êtes pas le correcteur de ce mini-tuto.';
				}
			}
			else
			{
				return 'Ce mini-tuto n\'a pas été corrigé ou a été recorrigé.';
			}
		}
		elseif($InfosSoumission['soumission_type_tuto'] == 2)
		{
			//Si le big-tutoriel a été corrigé mais n'a pas été recorrigé
			if(!empty($InfosSoumission['soumission_id_correction_1']) AND empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du big-tuto corrigé ainsi que les date de début et de fin de correction.
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige, correction_date_debut, correction_date_fin, correction_id_correcteur
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

				$stmt->execute();

				$InfosCorrection = $stmt->fetch(PDO::FETCH_ASSOC);

				if($InfosCorrection['correction_id_correcteur'] == $_SESSION['id'] || verifier('zcorrection_editer_tutos'))
				{
					if(!empty($InfosCorrection['correction_date_debut']) AND empty($InfosCorrection['correction_date_fin']))
					{
						//On supprime le big-tuto corrigé
						SupprimerBigTuto($InfosCorrection['correction_id_tuto_corrige']);

						//On copie le big-tuto original pour réinitialiser la correction.
						$tuto_duplique = DupliquerBigTuto($InfosSoumission['soumission_id_tuto']);

						//On met à jour la correction
						$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
						SET correction_id_tuto_corrige = :tuto_duplique, correction_date_debut = :vide, correction_date_fin = :vide,
						correction_commentaire = :vide, correction_correcteur_invisible = :un
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':tuto_duplique', $tuto_duplique);
						$stmt->bindValue(':vide', NULL, PDO::PARAM_NULL);
						$stmt->bindValue(':un', '1');
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

						$stmt->execute();

						return true;
					}
					else
					{
						return 'La correction n\'a pas été commencée ou elle est finie.';
					}
				}
				else
				{
					return 'Vous n\'êtes pas le correcteur de ce big-tuto.';
				}
			}
			else
			{
				return 'Ce big-tuto n\'a pas été corrigé ou a été recorrigé.';
			}
		}
		else
		{
			return 'Type de tutoriel inconnu.';
		}
	}
	else
	{
		return 'Cette soumission n\'existe pas.';
	}
}

function ReprendreDepuisZer0Recorrection($soumission_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On récupère les infos nécessaires sur la soumission
	$stmt = $dbh->prepare("SELECT soumission_id, soumission_id_tuto, soumission_id_correction_1, soumission_id_correction_2,
	soumission_type_tuto
	FROM zcov2_push_soumissions
	WHERE soumission_id = :soumission_id");
	$stmt->bindParam(':soumission_id', $soumission_id);
	$stmt->execute();


	$InfosSoumission = $stmt->fetch(PDO::FETCH_ASSOC);

	//Si la soumission existe
	if(!empty($InfosSoumission['soumission_id']))
	{
		//Si c'est un mini-tuto
		if($InfosSoumission['soumission_type_tuto'] == 1)
		{
			//Si le mini-tutoriel est en cours de recorrection
			if(!empty($InfosSoumission['soumission_id_correction_1']) AND !empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du mini-tuto recorrigé ainsi que les dates de début et de fin de recorrection.
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige, correction_date_debut, correction_date_fin, correction_id_correcteur
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

				$stmt->execute();

				$InfosRecorrection = $stmt->fetch(PDO::FETCH_ASSOC);

				if($InfosRecorrection['correction_id_correcteur'] == $_SESSION['id'])
				{
					if(!empty($InfosRecorrection['correction_date_debut']) AND empty($InfosRecorrection['correction_date_fin']))
					{
						//On supprime le mini-tuto recorrigé
						SupprimerMiniTuto($InfosRecorrection['correction_id_tuto_corrige']);

						//On récupère l'id du mini-tuto corrigé
						$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
						FROM zcov2_push_corrections
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

						$stmt->execute();

						$TutoCorrige = $stmt->fetch(PDO::FETCH_COLUMN);

						//On copie le mini-tuto corrigé pour réinitialiser la recorrection.
						$tuto_duplique = DupliquerMiniTuto($TutoCorrige);

						//On met à jour la recorrection
						$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
						SET correction_id_tuto_corrige = :tuto_duplique, correction_date_debut = :vide, correction_date_fin = :vide,
						correction_commentaire = :vide, correction_correcteur_invisible = :un
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':tuto_duplique', $tuto_duplique);
						$stmt->bindValue(':vide', NULL, PDO::PARAM_NULL);
						$stmt->bindValue(':un', '1');
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

						$stmt->execute();

						return true;
					}
					else
					{
						return 'La recorrection n\'a pas été commencée ou elle est finie.';
					}
				}
				else
				{
					return 'Vous n\'êtes pas le recorrecteur de ce mini-tuto.';
				}
			}
			else
			{
				return 'Ce mini-tuto n\'a pas été recorrigé.';
			}
		}
		elseif($InfosSoumission['soumission_type_tuto'] == 2)
		{
			//Si le big-tutoriel est en cours de recorrection
			if(!empty($InfosSoumission['soumission_id_correction_1']) AND !empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du big-tuto recorrigé ainsi que les date de début et de fin de recorrection.
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige, correction_date_debut, correction_date_fin, correction_id_correcteur
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

				$stmt->execute();

				$InfosRecorrection = $stmt->fetch(PDO::FETCH_ASSOC);

				if($InfosRecorrection['correction_id_correcteur'] == $_SESSION['id'])
				{
					if(!empty($InfosRecorrection['correction_date_debut']) AND empty($InfosRecorrection['correction_date_fin']))
					{
						//On supprime le big-tuto recorrigé
						SupprimerBigTuto($InfosRecorrection['correction_id_tuto_corrige']);

						//On récupère l'id du big-tuto corrigé
						$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
						FROM zcov2_push_corrections
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

						$stmt->execute();

						$TutoCorrige = $stmt->fetch(PDO::FETCH_COLUMN);

						//On copie le big-tuto corrigé pour réinitialiser la recorrection.
						$tuto_duplique = DupliquerBigTuto($TutoCorrige);

						//On met à jour la recorrection
						$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
						SET correction_id_tuto_corrige = :tuto_duplique, correction_date_debut = :vide, correction_date_fin = :vide,
						correction_commentaire = :vide, correction_correcteur_invisible = :un
						WHERE correction_id = :correction_id");
						$stmt->bindParam(':tuto_duplique', $tuto_duplique);
						$stmt->bindValue(':vide', NULL, PDO::PARAM_NULL);
						$stmt->bindValue(':un', '1');
						$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

						$stmt->execute();

						return true;
					}
					else
					{
						return 'La recorrection n\'a pas été commencée ou elle est finie.';
					}
				}
				else
				{
					return 'Vous n\'êtes pas le recorrecteur de ce big-tuto.';
				}
			}
			else
			{
				return 'Ce big-tuto n\'a pas été recorrigé.';
			}
		}
		else
		{
			return 'Type de tutoriel inconnu.';
		}
	}
	else
	{
		return 'Cette soumission n\'existe pas.';
	}
}

function SupprimerSoumission($soumission_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On récupère les infos nécessaires sur la soumission
	$stmt = $dbh->prepare("SELECT soumission_id, soumission_id_tuto, soumission_id_correction_1, soumission_id_correction_2,
	soumission_type_tuto, soumission_sauvegarde
	FROM zcov2_push_soumissions
	WHERE soumission_id = :soumission_id");
	$stmt->bindParam(':soumission_id', $soumission_id);
	$stmt->execute();


	$InfosSoumission = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$InfosSoumission['soumission_sauvegarde'] = stripslashes($InfosSoumission['soumission_sauvegarde']);

	//Si la soumission existe, on continue ! Sinon, NIARK NIARK NIARK !
	if(!empty($InfosSoumission['soumission_id']))
	{
		//On supprime la soumission
		$stmt = $dbh->prepare("DELETE FROM zcov2_push_soumissions
		WHERE soumission_id = :soumission_id");
		$stmt->bindParam(':soumission_id', $soumission_id);
		$stmt->execute();


		//Si c'est un mini-tuto
		if($InfosSoumission['soumission_type_tuto'] == 1)
		{
			//On supprime le mini-tuto original
			SupprimerMiniTuto($InfosSoumission['soumission_id_tuto']);

			//On supprime le fichier de la version originale du tuto.
			unlink(BASEPATH.'/web/tutos/'.$InfosSoumission['soumission_sauvegarde']);

			//Si le mini-tutoriel a été corrigé
			if(!empty($InfosSoumission['soumission_id_correction_1']))
			{
				//On récupère l'id du mini-tuto corrigé
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

				$stmt->execute();
				$MiniTutoCorrige = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$MiniTutoCorrige = $MiniTutoCorrige['correction_id_tuto_corrige'];

				//On supprime le mini-tuto corrigé
				SupprimerMiniTuto($MiniTutoCorrige);

				//On supprime la correction
				$stmt = $dbh->prepare("DELETE FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);
				$stmt->execute();

			}

			//Si le mini-tutoriel a été recorrigé
			if(!empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du mini-tuto recorrigé
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

				$stmt->execute();
				$MiniTutoRecorrige = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$MiniTutoRecorrige = $MiniTutoRecorrige['correction_id_tuto_corrige'];

				//On supprime le mini-tuto recorrigé
				SupprimerMiniTuto($MiniTutoRecorrige);

				//On supprime la recorrection
				$stmt = $dbh->prepare("DELETE FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);
				$stmt->execute();

			}
		}
		//Si c'est un big-tuto
		elseif($InfosSoumission['soumission_type_tuto'] == 2)
		{
			//On supprime le big-tuto original
			SupprimerBigTuto($InfosSoumission['soumission_id_tuto']);

			//On supprime le fichier de la version originale du big-tuto.
			unlink(BASEPATH.'/web/tutos/'.$InfosSoumission['soumission_sauvegarde']);

			//Si le big-tutoriel a été corrigé
			if(!empty($InfosSoumission['soumission_id_correction_1']))
			{
				//On récupère l'id du big-tuto corrigé
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);

				$stmt->execute();
				$BigTutoCorrige = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$BigTutoCorrige = $BigTutoCorrige['correction_id_tuto_corrige'];

				//On supprime le big-tuto corrigé
				SupprimerBigTuto($BigTutoCorrige);

				//On supprime la correction
				$stmt = $dbh->prepare("DELETE FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_1']);
				$stmt->execute();

			}

			//Si le big-tuto a été recorrigé
			if(!empty($InfosSoumission['soumission_id_correction_2']))
			{
				//On récupère l'id du big-tuto recorrigé
				$stmt = $dbh->prepare("SELECT correction_id_tuto_corrige
				FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);

				$stmt->execute();
				$BigTutoRecorrige = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				$BigTutoRecorrige = $BigTutoRecorrige['correction_id_tuto_corrige'];

				//On supprime le big-tuto recorrigé
				SupprimerBigTuto($BigTutoRecorrige);

				//On supprime la recorrection
				$stmt = $dbh->prepare("DELETE FROM zcov2_push_corrections
				WHERE correction_id = :correction_id");
				$stmt->bindParam(':correction_id', $InfosSoumission['soumission_id_correction_2']);
				$stmt->execute();

			}
		}
		return true;
	}
	else
	{

		return false;
	}
}

function SupprimerBigTuto($big_tuto_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On supprime le big-tuto
	$stmt = $dbh->prepare("DELETE FROM zcov2_push_big_tutos
	WHERE big_tuto_id = :big_tuto_id");
	$stmt->bindParam(':big_tuto_id', $big_tuto_id);
	$stmt->execute();


	//On récupère les parties du big-tuto
	$stmt = $dbh->prepare("SELECT partie_id
	FROM zcov2_push_big_tutos_parties
	WHERE partie_id_big_tuto = :big_tuto_id");
	$stmt->bindParam(':big_tuto_id', $big_tuto_id);
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$PartiesBigTuto[] = $resultat;
	}
	unset($resultat);
	$stmt->closeCursor();


	//Si le big-tutoriel a des parties (en même temps s'il en a pas je vois pas ce qu'il fout sur le site... :-° )
	if(!empty($PartiesBigTuto))
	{
		//On supprime les parties
		$stmt = $dbh->prepare("DELETE FROM zcov2_push_big_tutos_parties
		WHERE partie_id_big_tuto = :big_tuto_id");
		$stmt->bindParam(':big_tuto_id', $big_tuto_id);
		$stmt->execute();


		//Pour chaque partie du big-tuto
		foreach($PartiesBigTuto as $clef => $valeur)
		{
			//On récupère les chapitres (mini-tutos) de cette partie
			$stmt = $dbh->prepare("SELECT mini_tuto_id FROM zcov2_push_mini_tutos
			WHERE mini_tuto_id_partie = :partie_id");
			$stmt->bindParam(':partie_id', $valeur['partie_id']);
			$stmt->execute();
			while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$Chapitres[] = $resultat;
			}
			unset($resultat);
			$stmt->closeCursor();


			//Si cette partie a des chapitres
			if(!empty($Chapitres))
			{
				//Pour chaque chapitre (mini-tuto), on le supprime !
				foreach($Chapitres as $clef2 => $valeur2)
				{
					SupprimerMiniTuto($valeur2['mini_tuto_id']);
				}
				unset($Chapitres);
				/*Si on oublie cette ligne, c'est le drame. :p
				En effet un peu plus haut on a fait "$Chapitres[] = $resultat;".
				Donc à chaque tour de boucle du foreach, on va rajouter au tableau Chapitres les chapitres de toutes les parties !
				Problème réglé donc en détruisant le tableau Chapitres à la fin de chaque tour. :)
				*/
			}
		}
	}
}

function SupprimerMiniTuto($mini_tuto_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	//On supprime le mini-tuto
	$stmt = $dbh->prepare("DELETE FROM zcov2_push_mini_tutos
	WHERE mini_tuto_id = :mini_tuto_id");
	$stmt->bindParam(':mini_tuto_id', $mini_tuto_id);
	$stmt->execute();


	//On supprime les sous-parties du mini-tuto
	$stmt = $dbh->prepare("DELETE FROM zcov2_push_mini_tuto_sous_parties
	WHERE sous_partie_id_mini_tuto = :mini_tuto_id");
	$stmt->bindParam(':mini_tuto_id', $mini_tuto_id);
	$stmt->execute();


	//On récupère les questions du QCM du mini-tuto
	$stmt = $dbh->prepare("SELECT question_id
	FROM zcov2_push_qcm_questions
	WHERE question_id_mini_tuto = :mini_tuto_id");
	$stmt->bindParam(':mini_tuto_id', $mini_tuto_id);
	$stmt->execute();
	while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$questionsQCM[] = $resultat;
	}
	unset($resultat);
	$stmt->closeCursor();

	//Si le tuto a un QCM
	if(!empty($questionsQCM))
	{
		//On supprime les questions
		$stmt = $dbh->prepare("DELETE FROM zcov2_push_qcm_questions
		WHERE question_id_mini_tuto = :mini_tuto_id");
		$stmt->bindParam(':mini_tuto_id', $mini_tuto_id);
		$stmt->execute();

		//On supprime les réponses
		$stmt = $dbh->prepare("DELETE FROM zcov2_push_qcm_reponses
		WHERE reponse_id_qcm_question = :reponse_id_qcm_question");
		foreach($questionsQCM as $clef => &$valeur)
		{
			$stmt->bindParam(':reponse_id_qcm_question', $valeur['question_id']);
			$stmt->execute();

		}
	}
	unset($questionsQCM);
}

function ListerSoumissionsUtilisateur($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT DISTINCT
	soumission_id,
	soumission_description,
	soumission_date,
	soumission_type_tuto,
	soumission_prioritaire,
	soumission_recorrection,
	soumission_avancement,
	soumission_sauvegarde,
	soumission_id_tuto,
	soumission_etat,
	mt.mini_tuto_titre as mini_tuto_titre,
	bt.big_tuto_titre as big_tuto_titre,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_id_tuto_corrige AS id_tuto_corrige,
	c2.correction_id_tuto_corrige AS id_tuto_recorrige,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	u1.utilisateur_pseudo AS utilisateur_pseudo,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee,
	u3.utilisateur_id AS id_correcteur,
	u3.utilisateur_pseudo AS pseudo_correcteur,
	u4.utilisateur_pseudo AS pseudo_recorrecteur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u1 ON soumission_id_utilisateur = u1.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u2 ON soumission_id_utilisateur = u2.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u3 ON c1.correction_id_correcteur = u3.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u4 ON c2.correction_id_correcteur = u4.utilisateur_id
	WHERE s.soumission_id_utilisateur = :id
	ORDER BY soumission_prioritaire DESC, soumission_recorrection DESC, s.soumission_date ASC");

	$stmt->bindParam(':id', $id);

	$stmt->execute();

	return $stmt->fetchAll();
}
