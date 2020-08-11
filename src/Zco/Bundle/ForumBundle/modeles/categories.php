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

/**
 * Modèle concernant tout ce qui est catégories pour le forum.
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 * @begin 25/06/07
 * @last 01/01/09
 */

function ListerCategoriesForum($InfosCategorie = array(), $LimiterSousCat = false)
{
	//Récupération des catégories
	$dbh = Doctrine_Manager::connection()->getDbh();
	if(!empty($_GET['trash']))
		$add = "(SELECT COUNT(*) FROM zcov2_forum_sujets WHERE sujet_corbeille = 1 AND sujet_forum_id = cat_id) AS nb_sujets_corbeille, ";
	elseif(!empty($_GET['favori']))
		$add = "AND lunonlu_favori = 1";
	else
		$add = '';
	if(!empty($InfosCategorie))
		$add2 = 'AND cat_gauche > :gauche AND cat_droite < :droite ';
	else
		$add2 = '';

	if($LimiterSousCat)
		$add3 = 'AND cat_niveau <= 4';
	else
		$add3 = '';
	//Droit
	$droit = !empty($_GET['trash']) ? 'corbeille_sujets' : 'voir_sujets';

	$groupes = isset($_SESSION['groupes_secondaires'])
		? $_SESSION['groupes_secondaires']
		: array();
	array_unshift($groupes, $_SESSION['groupe']);
	$groupes = implode(',', $groupes);

	//Archives
	$archives = !empty($_GET['archives']) ? 1 : 0;

	$stmt = $dbh->prepare("SELECT cat_id, cat_nom, cat_gauche, cat_droite, cat_description, cat_last_element, cat_url, " .
			"cat_niveau, cat_redirection, cat_archive, message_date, UNIX_TIMESTAMP(message_date) AS message_timestamp, message_auteur, utilisateur_id, " .
			"IFNULL(utilisateur_pseudo, 'Anonyme') AS utilisateur_pseudo, " .
			"sujet_titre, message_id, message_sujet_id, ".$add." " .
			"lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe, lunonlu_favori, groupe_class " .
			"FROM zcov2_categories " .
			"LEFT JOIN zcov2_forum_messages ON cat_last_element = message_id " .
			"LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id " .
			"LEFT JOIN zcov2_utilisateurs ON message_auteur = utilisateur_id " .
			"LEFT JOIN zcov2_forum_lunonlu ON sujet_id = lunonlu_sujet_id AND lunonlu_utilisateur_id = :user_id " .
			"LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = cat_id AND gd_id_groupe IN ($groupes) " .
			"LEFT JOIN zcov2_droits ON gd_id_droit = droit_id " .
			"LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id " .
			"WHERE cat_niveau > 1 ".$add3." AND droit_nom = :droit AND gd_valeur = 1 AND cat_archive = :archives ". $add2 .
			"GROUP BY cat_id ".
			"ORDER BY cat_gauche");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':droit', $droit);
	$stmt->bindParam(':archives', $archives);
	
	if(!empty($InfosCategorie))
	{
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
	}
	$stmt->execute();
	$ret = $stmt->fetchAll();

	$niveau_limite = !empty($InfosCategorie) ? $InfosCategorie['cat_niveau'] + 1 : 3;
	foreach ($ret as $i => $cat)
	{
		if ($cat['cat_niveau'] == $niveau_limite)
		{
			$current_forum = $i;
			$ret[$i]['sous_forums'] = array();
		}
		elseif ($cat['cat_niveau'] > $niveau_limite && isset($current_forum))
		{
			$ret[$current_forum]['sous_forums'][] = $cat;
			unset($ret[$i]);
		}
	}

	return $ret;
}

//Cette fonction retourne l'image du système lu/non lu.
function LuNonluCategorie($lu)
{
	if($lu['derniere_lecture_globale'] > $lu['date_dernier_message'])
	{
		$dejalu = true;
	}
	else
	{
		$dejalu = false;
	}
	//Si on a déjà lu au moins une fois ce sujet
	if(!empty($lu['lunonlu_utilisateur_id']) || $dejalu)
	{
		//Si on pas encore posté dans ce sujet
		if(!$lu['lunonlu_participe'])
		{
			//Si il n'y a pas de nouveau message depuis la dernière visite du membre
			if(($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu)
			{
				$retour = array(
					'image' => 'lightbulb_off',
					'title' => 'Pas de nouvelles réponses, jamais participé'
				);
			}
			//Si il y a un ou des nouveaux messages depuis la dernière visite du membre
			else
			{
				$retour = array(
					'image' => 'lightbulb',
					'title' => 'Nouvelles réponses, jamais participé'
				);
			}
		}
		//Si on a déjà posté dans ce sujet
		else
		{
			//Si il n'y a pas de nouveau message depuis la dernière visite du membre
			if(($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu)
			{
				$retour = array(
					'image' => 'lightbulb_off_add',
					'title' => 'Pas de nouvelles réponses, participé'
				);
			}
			//Si il y a un ou des nouveaux messages depuis la dernière visite du membre
			else
			{
				$retour = array(
					'image' => 'lightbulb_add',
					'title' => 'Nouvelles réponses, participé'
				);
			}
		}
	}
	//Si on n'est jamais  allé sur un sujet, il est non-lu.
	else
	{
		$retour = array(
			'image' => 'lightbulb',
			'title' => 'Nouvelles réponses, jamais participé'
		);
	}
	return $retour;
}

function ListeCategoriesForums()
{
	return ListerCategoriesForum();
}

function RecupererSautRapide($id)
{
	if (empty($_SESSION['groupes_secondaires']))
	{
		if(($ListerCategories = Container::getService('zco_core.cache')->Get('saut_rapide_'.$_SESSION['groupe'])) === false)
		{
			$ListerCategories = ListerCategoriesForum();
			Container::getService('zco_core.cache')->Set('saut_rapide_'.$_SESSION['groupe'], $ListerCategories, 3600);
		}
	}
	else
	{
		if(($ListerCategories = Container::getService('zco_core.cache')->Get('saut_rapide_utilisateur_'.$_SESSION['id'])) === false)
		{
			$ListerCategories = ListerCategoriesForum();
			Container::getService('zco_core.cache')->Set('saut_rapide_utilisateur_'.$_SESSION['id'], $ListerCategories, 3600);
		}
	}

	$SautRapide = '';
	if(!empty($ListerCategories))
	{
		$SautRapide = '<div class="saut_forum"><form method="post" action="/forum/">
		<p>
		<select name="saut_forum" onchange="document.location=\'/forum/\' + this.value;">
		<option value="">Accueil des forums</option>';

		$nb = 0;
		foreach($ListerCategories as $clef => $valeur)
		{
			//Dans ce if on ne liste que les catégories
			if($valeur['cat_niveau'] == 2)
			{
				if($nb != 0)
					$SautRapide .= '</optgroup>';
				$SautRapide .= '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
			}
			//Ici on liste les forums
			else
			{
				if($valeur['cat_id'] == $id)
				{
					$selected = ' selected="selected"';
				}
				else
				{
					$selected = '';
				}
				$SautRapide .= '<option value="'.str_replace(array('%id%', '%id2%', '%nom%'), array($valeur['cat_id'], !empty($_GET['id2']) ? $_GET['id2'] : 0, rewrite($valeur['cat_nom'])), $valeur['cat_url']).'"'.$selected.'>'.htmlspecialchars($valeur['cat_nom']).'</option>';
				if (!empty($valeur['sous_forums']))
				{
					foreach ($valeur['sous_forums'] as $forum)
					{
						if($forum['cat_id'] == $id)
						{
							$selected = ' selected="selected"';
						}
						else
						{
							$selected = '';
						}
						$SautRapide .= '<option value="'.str_replace(array('%id%', '%id2%', '%nom%'), array($forum['cat_id'], !empty($_GET['id2']) ? $_GET['id2'] : 0, rewrite($forum['cat_nom'])), $forum['cat_url']).'"'.$selected.'>'.str_pad('', ($forum['cat_niveau']-3)*3, '...').' '.htmlspecialchars($forum['cat_nom']).'</option>';
					}
				}
			}
			$nb++;
		}
		$SautRapide .= '</optgroup></select><noscript>'
		              .'<input type="submit" value="Aller"/>'
		              .'</noscript></p></form></div>';
	}

	return $SautRapide;
}