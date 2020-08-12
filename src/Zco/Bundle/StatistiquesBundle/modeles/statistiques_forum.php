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

use Doctrine\Common\Cache\Cache;

function RecupererStatistiquesForum(Cache $cache)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$retour = array();

	$finJour = strtotime('tomorrow') - time();

	//Nombre de topics
	if(false === ($nombreTopics = $cache->fetch('forum_nombre_topics')))
	{
		$stmt = $dbh->prepare("SELECT COUNT(*) AS nb FROM zcov2_forum_sujets");
		$stmt->execute();
		$nombreTopics = $stmt->fetchColumn();
        $cache->save('forum_nombre_topics', $nombreTopics, $finJour);
	}
	$retour['nb_topics'] = $nombreTopics;

	//Nombre de posts
	if(false === ($nombrePosts = $cache->fetch('forum_nombre_posts')))
	{
		$stmt = $dbh->prepare("SELECT COUNT(*) AS nb FROM zcov2_forum_messages");
		$stmt->execute();
		$nombrePosts = $stmt->fetchColumn();
        $cache->save('forum_nombre_posts', $nombrePosts, $finJour);
	}
	$retour['nb_posts'] = $nombrePosts;

	$nbJours = false;
	//Nombre de topics par jour (on prendra la plus ancienne date de message comme date de départ)
	if(!($nombreTopicsParJour = $cache->fetch('forum_nombre_topics_par_jour')) OR !($nombrePostsParJour = $cache->fetch('forum_nombre_posts_par_jour')))
	{
		$stmt = $dbh->prepare("SELECT DATEDIFF(NOW(), message_date) as nb_jours
				FROM zcov2_forum_messages
				ORDER BY message_date
				LIMIT 1");
		$stmt->execute();
		$nb_jours = $stmt->fetchColumn();

		if (false === ($nombreTopicsParJour = $cache->fetch('forum_nombre_topics_par_jour')))
		{
			$nombreTopicsParJour = 0;
			if ($nb_jours > 0)
			{
				$nombreTopicsParJour = round($retour['nb_topics'] / $nb_jours, 2);
			}
            $cache->save('forum_nombre_topics_par_jour', $nombreTopicsParJour, $finJour);
		}
		if (false === ($nombrePostsParJour = $cache->fetch('forum_nombre_posts_par_jour')))
		{
			$nombrePostsParJour = 0;
			if ($nb_jours > 0)
			{
				$nombrePostsParJour = round($retour['nb_posts'] / $nb_jours, 2);
			}
            $cache->save('forum_nombre_posts_par_jour', $nombrePostsParJour, $finJour);
		}
	}
	$retour['nb_topics_jour'] = $nombreTopicsParJour;

	//Nombre de posts par jour
	$retour['nb_posts_jour'] = $nombrePostsParJour;

	//Deux derniers topics actifs
	//--- Récupération
	if(!verifier('connecte'))
		$lunonlu_user = 0;
	else
		$lunonlu_user = $_SESSION['id'];

	$stmt = $dbh->prepare("SELECT DISTINCT cat_nom, sujet_id, sujet_titre, sujet_dernier_message
		FROM zcov2_forum_sujets
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id
		LEFT JOIN zcov2_droits ON droit_nom = 'voir_sujets'
		LEFT JOIN zcov2_groupes_droits ON gd_id_droit = droit_id AND gd_id_groupe = :id_grp AND gd_id_categorie = cat_id
		WHERE sujet_corbeille = 0 AND sujet_ferme = 0 AND gd_valeur = 1
		ORDER BY message_date DESC
		LIMIT 2");
	$stmt->bindParam(':id_grp', $_SESSION['groupe']);
	$stmt->execute();

	$messages = $stmt->fetchAll();
	$last_posts = array();

	foreach($messages as $msg)
		$last_posts[$msg['sujet_id']] = $msg;
	unset($messages);


	//Deux derniers topics créés

	$stmt = $dbh->prepare("SELECT DISTINCT cat_nom, sujet_id, sujet_titre,
		sujet_dernier_message
		FROM zcov2_forum_sujets
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		LEFT JOIN zcov2_droits ON droit_nom = 'voir_sujets'
		LEFT JOIN zcov2_groupes_droits ON gd_id_droit = droit_id AND gd_id_groupe = :id_grp AND gd_id_categorie = cat_id
		WHERE sujet_corbeille = 0 AND sujet_ferme = 0 AND gd_valeur = 1
		ORDER BY sujet_date DESC
		LIMIT 2");
	$stmt->bindParam(':id_grp', $_SESSION['groupe']);
	$stmt->execute();

	$messages = $stmt->fetchAll();
	$last_topics = array();

	foreach($messages as $msg)
		$last_topics[$msg['sujet_id']] = $msg;
	unset($messages);


	//Topics coup de coeur
	//--- Récupération
	if(!verifier('connecte'))
			$lunonlu_user = 0;
	else
		$lunonlu_user = $_SESSION['id'];

	$stmt = $dbh->prepare("SELECT DISTINCT cat_nom, sujet_id, sujet_titre,
		sujet_dernier_message
		FROM zcov2_forum_sujets
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		LEFT JOIN zcov2_droits ON droit_nom = 'voir_sujets'
		LEFT JOIN zcov2_groupes_droits ON gd_id_droit = droit_id AND gd_id_groupe = :id_grp AND gd_id_categorie = cat_id
		WHERE sujet_corbeille = 0 AND sujet_coup_coeur = 1 AND gd_valeur = 1
		ORDER BY RAND()
		LIMIT 2");
	$stmt->bindParam(':id_grp', $_SESSION['groupe']);
	$stmt->execute();

	$messages = $stmt->fetchAll();
	$coup_coeur = array();

	foreach($messages as $msg)
		$coup_coeur[$msg['sujet_id']] = $msg;
	unset($messages);

	// Lu - Non lu pour les requêtes du dessus
	if(verifier('connecte'))
	{
		$sids = array_merge(
			array_keys($last_posts),
			array_keys($last_topics),
			array_keys($coup_coeur));
		
		if (!empty($sids))
		{
			$stmt = $dbh->prepare('SELECT lunonlu_sujet_id, '
				.'lunonlu_message_id, lunonlu_participe '
				.'FROM '.Container::getParameter('database.prefix').'forum_lunonlu '
				.'WHERE lunonlu_utilisateur_id = :id_user '
					.' AND lunonlu_sujet_id IN('
					.implode(', ', $sids)
					.')');
			$stmt->bindParam(':id_user', $_SESSION['id']);
			$stmt->execute();
			while($d = $stmt->fetch())
			{
				if(isset($last_posts[$d['lunonlu_sujet_id']]))
				$last_posts[$d['lunonlu_sujet_id']] = array_merge(
					$last_posts[$d['lunonlu_sujet_id']], $d);
				if(isset($last_topics[$d['lunonlu_sujet_id']]))
				$last_topics[$d['lunonlu_sujet_id']] = array_merge(
					$last_topics[$d['lunonlu_sujet_id']], $d);
				if(isset($coup_coeur[$d['lunonlu_sujet_id']]))
				$coup_coeur[$d['lunonlu_sujet_id']] = array_merge(
					$coup_coeur[$d['lunonlu_sujet_id']], $d);
			}
		}
	}

	$retour['last_posts'] = $last_posts;
	$retour['last_topics'] = $last_topics;
	$retour['topics_coup_coeur'] = $coup_coeur;

	return $retour;
}
