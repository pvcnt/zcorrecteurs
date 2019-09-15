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

namespace Zco\Bundle\ForumBundle\Domain;

/**
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
final class ForumDAO
{
    public static function ListerCategoriesForum($InfosCategorie = array(), $LimiterSousCat = false)
    {
        //Récupération des catégories
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if (!empty($_GET['trash']))
            $add = "(SELECT COUNT(*) FROM zcov2_forum_sujets WHERE sujet_corbeille = 1 AND sujet_forum_id = cat_id) AS nb_sujets_corbeille, ";
        elseif (!empty($_GET['favori']))
            $add = "AND lunonlu_favori = 1";
        else
            $add = '';
        if (!empty($InfosCategorie))
            $add2 = 'AND cat_gauche > :gauche AND cat_droite < :droite ';
        else
            $add2 = '';

        if ($LimiterSousCat)
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
            "sujet_titre, message_id, message_sujet_id, " . $add . " " .
            "lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe, lunonlu_favori " .
            "FROM zcov2_categories " .
            "LEFT JOIN zcov2_forum_messages ON cat_last_element = message_id " .
            "LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id " .
            "LEFT JOIN zcov2_utilisateurs ON message_auteur = utilisateur_id " .
            "LEFT JOIN zcov2_forum_lunonlu ON sujet_id = lunonlu_sujet_id AND lunonlu_utilisateur_id = :user_id " .
            "LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = cat_id AND gd_id_groupe IN ($groupes) " .
            "LEFT JOIN zcov2_droits ON gd_id_droit = droit_id " .
            "WHERE cat_niveau > 1 " . $add3 . " AND droit_nom = :droit AND gd_valeur = 1 AND cat_archive = :archives " . $add2 .
            "GROUP BY cat_id " .
            "ORDER BY cat_gauche");
        $stmt->bindParam(':user_id', $_SESSION['id']);
        $stmt->bindParam(':droit', $droit);
        $stmt->bindParam(':archives', $archives);

        if (!empty($InfosCategorie)) {
            $stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
            $stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
        }
        $stmt->execute();
        $ret = $stmt->fetchAll();

        $niveau_limite = !empty($InfosCategorie) ? $InfosCategorie['cat_niveau'] + 1 : 3;
        foreach ($ret as $i => $cat) {
            if ($cat['cat_niveau'] == $niveau_limite) {
                $current_forum = $i;
                $ret[$i]['sous_forums'] = array();
            } elseif ($cat['cat_niveau'] > $niveau_limite && isset($current_forum)) {
                $ret[$current_forum]['sous_forums'][] = $cat;
                unset($ret[$i]);
            }
        }

        return $ret;
    }

    //Cette fonction retourne l'image du système lu/non lu.
    public static function LuNonluCategorie($lu)
    {
        if ($lu['derniere_lecture_globale'] > $lu['date_dernier_message']) {
            $dejalu = true;
        } else {
            $dejalu = false;
        }
        //Si on a déjà lu au moins une fois ce sujet
        if (!empty($lu['lunonlu_utilisateur_id']) || $dejalu) {
            //Si on pas encore posté dans ce sujet
            if (!$lu['lunonlu_participe']) {
                //Si il n'y a pas de nouveau message depuis la dernière visite du membre
                if (($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu) {
                    $retour = array(
                        'image' => 'lightbulb_off',
                        'title' => 'Pas de nouvelles réponses, jamais participé'
                    );
                } //Si il y a un ou des nouveaux messages depuis la dernière visite du membre
                else {
                    $retour = array(
                        'image' => 'lightbulb',
                        'title' => 'Nouvelles réponses, jamais participé'
                    );
                }
            } //Si on a déjà posté dans ce sujet
            else {
                //Si il n'y a pas de nouveau message depuis la dernière visite du membre
                if (($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu) {
                    $retour = array(
                        'image' => 'lightbulb_off_add',
                        'title' => 'Pas de nouvelles réponses, participé'
                    );
                } //Si il y a un ou des nouveaux messages depuis la dernière visite du membre
                else {
                    $retour = array(
                        'image' => 'lightbulb_add',
                        'title' => 'Nouvelles réponses, participé'
                    );
                }
            }
        } //Si on n'est jamais  allé sur un sujet, il est non-lu.
        else {
            $retour = array(
                'image' => 'lightbulb',
                'title' => 'Nouvelles réponses, jamais participé'
            );
        }
        return $retour;
    }

    public static function RecupererSautRapide($id)
    {
        //TODO: reuse global categories cache here.
        $ListerCategories = ForumDAO::ListerCategoriesForum();

        $SautRapide = '';
        if (!empty($ListerCategories)) {
            $SautRapide = '<div class="saut_forum"><form method="post" action="/forum/">
		<p>
		<select name="saut_forum" onchange="document.location=\'/forum/\' + this.value;">
		<option value="">Accueil des forums</option>';

            $nb = 0;
            foreach ($ListerCategories as $clef => $valeur) {
                //Dans ce if on ne liste que les catégories
                if ($valeur['cat_niveau'] == 2) {
                    if ($nb != 0)
                        $SautRapide .= '</optgroup>';
                    $SautRapide .= '<optgroup label="' . htmlspecialchars($valeur['cat_nom']) . '">';
                } //Ici on liste les forums
                else {
                    if ($valeur['cat_id'] == $id) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    $SautRapide .= '<option value="' . str_replace(array('%id%', '%id2%', '%nom%'), array($valeur['cat_id'], !empty($_GET['id2']) ? $_GET['id2'] : 0, rewrite($valeur['cat_nom'])), $valeur['cat_url']) . '" ' . $selected . '>' . htmlspecialchars($valeur['cat_nom']) . '</option>';
                    if (!empty($valeur['sous_forums'])) {
                        foreach ($valeur['sous_forums'] as $forum) {
                            if ($forum['cat_id'] == $id) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            $SautRapide .= '<option value="' . str_replace(array('%id%', '%id2%', '%nom%'), array($forum['cat_id'], !empty($_GET['id2']) ? $_GET['id2'] : 0, rewrite($forum['cat_nom'])), $forum['cat_url']) . '" ' . $selected . '>' . str_pad('', ($forum['cat_niveau'] - 3) * 3, '...') . ' ' . htmlspecialchars($forum['cat_nom']) . '</option>';
                        }
                    }
                }
                $nb++;
            }
            $SautRapide .= '</optgroup></select><noscript>'
                . '<input type="submit" value="Aller"/>'
                . '</noscript></p></form></div>';
        }

        return $SautRapide;
    }

    public static function CompterSujets($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // Corbeille
        if (!empty($_GET['trash']) AND verifier('corbeille_sujets', $_GET['id']))
            $trash = 1;
        else
            $trash = 0;

        // S'il n'y a pas d'id, c'est que l'on ne veut pas voir un forum en particulier mais tous les sujets de tous les forums
        if (empty($id)) {
            $add = "";

            if (isset($_GET['closed'])) {
                $add .= ' AND sujet_ferme = ' . ($_GET['closed'] ? 1 : 0);
            }
            if (isset($_GET['solved'])) {
                $add .= ' AND sujet_resolu = ' . ($_GET['solved'] ? 1 : 0);
            }
            if (isset($_GET['favori'])) {
                $add .= ' AND lunonlu_favori = ' . ($_GET['favori'] ? 1 : 0);
            }
            if (isset($_GET['coeur'])) {
                $add .= ' AND sujet_coup_coeur = ' . ($_GET['coeur'] ? 1 : 0);
            }
            if (isset($_GET['epingle'])) {
                $add .= ' AND sujet_annonce = ' . ($_GET['epingle'] ? 1 : 0);
            }
        } else {
            $add = "sujet_forum_id = " . $id . " AND ";
        }

        $groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
        array_unshift($groupes, $_SESSION['groupe']);
        $groupes = implode(',', $groupes);

        $stmt = $dbh->prepare("SELECT COUNT(DISTINCT sujet_id) AS nombre_sujets " .
            "FROM zcov2_forum_sujets " .
            'LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = sujet_forum_id ' .
            "AND gd_id_groupe IN ($groupes) " .
            'LEFT JOIN zcov2_droits ON gd_id_droit = droit_id ' .
            "WHERE sujet_forum_id = :f AND sujet_corbeille = :trash " .
            "AND droit_nom = 'voir_sujets'" . $add
        );
        $stmt->bindParam(':f', $id);
        $stmt->bindParam(':trash', $trash);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function ListerSujets($PremierMess, $MessaAfficher, $forumID = null)
    {
        // Savoir si on regarde un forum en particulier ou le forum globalement
        $forums = $forumID ? 'sujet_forum_id = ' . (int)$forumID . ' AND' : '';

        // Ajouts au WHERE par des flags envoyées par $_GET
        $add = '';
        if (isset($_GET['closed'])) {
            $add .= ' AND sujet_ferme = ' . (int)($_GET['closed'] ? 1 : 0);
        }
        if (isset($_GET['solved'])) {
            $add .= ' AND sujet_resolu = ' . ($_GET['solved'] ? 1 : 0);
        }
        if (isset($_GET['favori'])) {
            $add .= ' AND lunonlu_favori = ' . ($_GET['favori'] ? 1 : 0);
        }
        if (isset($_GET['coeur'])) {
            $add .= ' AND sujet_coup_coeur = ' . ($_GET['coeur'] ? 1 : 0);
        }
        if (isset($_GET['epingle'])) {
            $add .= ' AND sujet_annonce = ' . ($_GET['epingle'] ? 1 : 0);
        }

        /* Fin des ajouts */

        // Corbeille
        if (!empty($_GET['trash']) AND verifier('corbeille_sujets', $_GET['id'])) {
            $trash = 1;
        } else {
            $trash = 0;
        }

        if (!verifier('connecte')) {
            $lunonlu_user = 0;
        } else {
            $lunonlu_user = $_SESSION['id'];
        }
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
        array_unshift($groupes, $_SESSION['groupe']);
        $groupes = implode(',', $groupes);
        $stmt = $dbh->prepare('SELECT sujet_id, sujet_titre, sujet_sous_titre, ' .
            'sujet_coup_coeur, sujet_reponses, sujet_auteur, sujet_forum_id, ' .
            'Ma.utilisateur_id_groupe AS sujet_auteur_groupe, ' .
            'Ma.utilisateur_id AS sujet_auteur_pseudo_existe, ' .
            'Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, ' .
            "COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, " .
            "COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, " .
            'message_auteur AS sujet_dernier_message_auteur_id, sujet_date, ' .
            'message_date, UNIX_TIMESTAMP(message_date) AS message_timestamp, ' .
            'sujet_dernier_message, sujet_sondage, sujet_annonce, ' .
            'sujet_ferme, sujet_resolu, message_id, ' .
            'lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, ' .
            'lunonlu_participe, lunonlu_favori ' .
            'FROM zcov2_forum_sujets ' .
            'LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id ' .
            'LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id ' .
            'LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id ' .
            'LEFT JOIN zcov2_forum_lunonlu ON sujet_id = lunonlu_sujet_id ' .
            'AND ' . $lunonlu_user . ' = lunonlu_utilisateur_id ' .
            'LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = sujet_forum_id ' .
            "AND gd_id_groupe IN($groupes) " .
            'LEFT JOIN zcov2_droits ON gd_id_droit = droit_id ' .
            'WHERE ' . $forums . ' sujet_corbeille = :trash' . $add . ' ' .
            "AND droit_nom = 'voir_sujets' " .
            'AND gd_valeur = 1 ' .
            'GROUP BY sujet_id ' .
            'ORDER BY sujet_annonce DESC, message_date DESC ' .
            'LIMIT ' . $PremierMess . ' , ' . $MessaAfficher);
        $stmt->bindParam(':trash', $trash);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Cette fonction retourne l'image du système lu/non lu.
    public static function LuNonluForum($lu)
    {
        $dejalu = $lu['derniere_lecture_globale'] > $lu['date_dernier_message'];
        $dejavu = !empty($lu['lunonlu_utilisateur_id']);

        if ($dejavu || $dejalu) {
            // Si on a déjà lu au moins une fois ce sujet
            if (!$lu['lunonlu_participe']) {
                // Si on n'a pas encore posté dans ce sujet
                if ($dejalu || $lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) {
                    // Si il n'y a pas de nouveau message depuis la dernière visite du membre
                    $retour = array(
                        'image' => 'pas_nouveau_message.png',
                        'title' => 'Pas de nouvelles réponses, jamais participé',
                        'fleche' => '0'
                    );
                } else {
                    // Si il y a un ou des nouveaux messages depuis la dernière visite du membre
                    $retour = array(
                        'image' => 'nouveau_message.png',
                        'title' => 'Nouvelles réponses, jamais participé',
                        'fleche' => '1'
                    );
                }
            } else {
                // Si on a déjà posté dans ce sujet
                if (($lu['lunonlu_message_id'] == $lu['sujet_dernier_message']) || $dejalu) {
                    // Si il n'y a pas de nouveau message depuis la dernière visite du membre
                    $retour = array(
                        'image' => 'repondu_pas_nouveau_message.png',
                        'title' => 'Pas de nouvelles réponses, participé',
                        'fleche' => '0'
                    );
                } else {
                    // Si il y a un ou des nouveaux messages depuis la dernière visite du membre
                    $retour = array(
                        'image' => 'repondu_nouveau_message.png',
                        'title' => 'Nouvelles réponses, participé',
                        'fleche' => '1'
                    );
                }
            }
        } else {
            $retour = array(
                'image' => 'nouveau_message.png',
                'title' => 'Nouvelles réponses, jamais participé',
                'fleche' => '0'
            );
        }
        return $retour;
    }

    // Liste les id et titres des sujets du forum
    public static function ListerSujetsId($forums)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        if (!empty($forums) && is_array($forums)) {
            $stmt = $dbh->prepare("
			SELECT sujet_id, sujet_titre
			FROM zcov2_forum_sujets
			WHERE sujet_forum_id IN(" . implode(',', $forums) . ")
			AND sujet_corbeille = 0
			ORDER BY sujet_date DESC
		");

            $stmt->execute();

            return $stmt->fetchAll();
        } else
            return false;
    }

    public static function ListerSujetsIn($in)
    {
        if (empty($in)) {
            return [];
        }

        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("SELECT sujet_id, sujet_titre, sujet_sous_titre, " .
            "sujet_reponses, sujet_auteur, " .
            "Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
            "Ma.utilisateur_id AS sujet_auteur_pseudo_existe, " .
            "Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, " .
            "COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, " .
            "COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, " .
            "message_auteur AS sujet_dernier_message_auteur_id, " .
            "sujet_date, message_date, sujet_dernier_message, sujet_sondage, " .
            "sujet_annonce, sujet_ferme, sujet_resolu, message_id, " .
            "cat_id, cat_nom " .
            "FROM zcov2_forum_sujets " .
            "LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
            "LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id " .
            "LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id " .
            "LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id " .
            "WHERE sujet_id IN(" . implode(', ', $in) . ") " .
            "ORDER BY sujet_annonce DESC, message_date DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function ListerSujetsTitre($titre)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("SELECT sujet_id, sujet_titre, sujet_sous_titre, " .
            "sujet_reponses, sujet_auteur, " .
            "Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
            "Ma.utilisateur_id AS sujet_auteur_pseudo_existe, " .
            "Mb.utilisateur_id AS sujet_dernier_message_pseudo_existe, " .
            "COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, " .
            "COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS sujet_dernier_message_pseudo, " .
            "message_auteur AS sujet_dernier_message_auteur_id, " .
            "sujet_date, message_date, sujet_dernier_message, sujet_sondage, " .
            "sujet_annonce, sujet_ferme, sujet_resolu, message_id, " .
            "cat_id, cat_nom " .
            "FROM zcov2_forum_sujets " .
            "LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
            "LEFT JOIN zcov2_forum_messages ON sujet_dernier_message = message_id " .
            "LEFT JOIN zcov2_utilisateurs Ma ON sujet_auteur = Ma.utilisateur_id " .
            "LEFT JOIN zcov2_utilisateurs Mb ON message_auteur = Mb.utilisateur_id " .
            "WHERE sujet_titre LIKE " . $dbh->quote('%' . $titre . '%') . " " .
            "ORDER BY sujet_annonce DESC, message_date DESC");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function RecupererStatistiquesForum()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $cache = \Container::cache();
        $retour = array();
        $finJour = strtotime('tomorrow') - time();

        //Nombre de topics
        if (false === ($nombreTopics = $cache->fetch('forum_nombre_topics'))) {
            $stmt = $dbh->prepare("SELECT COUNT(*) AS nb FROM zcov2_forum_sujets");
            $stmt->execute();
            $nombreTopics = $stmt->fetchColumn();
            $cache->save('forum_nombre_topics', $nombreTopics, $finJour);
        }
        $retour['nb_topics'] = $nombreTopics;

        //Nombre de posts
        if (false === ($nombrePosts = $cache->fetch('forum_nombre_posts'))) {
            $stmt = $dbh->prepare("SELECT COUNT(*) AS nb FROM zcov2_forum_messages");
            $stmt->execute();
            $nombrePosts = $stmt->fetchColumn();
            $cache->save('forum_nombre_posts', $nombrePosts, $finJour);
        }
        $retour['nb_posts'] = $nombrePosts;

        //Nombre de topics par jour (on prendra la plus ancienne date de message comme date de départ)
        if (!($nombreTopicsParJour = $cache->fetch('forum_nombre_topics_par_jour')) OR !($nombrePostsParJour = $cache->fetch('forum_nombre_posts_par_jour'))) {
            $stmt = $dbh->prepare("SELECT DATEDIFF(NOW(), message_date) as nb_jours
				FROM zcov2_forum_messages
				ORDER BY message_date
				LIMIT 1");
            $stmt->execute();
            $nb_jours = $stmt->fetchColumn();

            if (false === ($nombreTopicsParJour = $cache->fetch('forum_nombre_topics_par_jour'))) {
                $nombreTopicsParJour = 0;
                if ($nb_jours > 0) {
                    $nombreTopicsParJour = round($retour['nb_topics'] / $nb_jours, 2);
                }
                $cache->save('forum_nombre_topics_par_jour', $nombreTopicsParJour, $finJour);
            }
            if (false === ($nombrePostsParJour = $cache->fetch('forum_nombre_posts_par_jour'))) {
                $nombrePostsParJour = 0;
                if ($nb_jours > 0) {
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

        foreach ($messages as $msg)
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

        foreach ($messages as $msg)
            $last_topics[$msg['sujet_id']] = $msg;
        unset($messages);


        //Topics coup de coeur
        //--- Récupérations
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

        foreach ($messages as $msg)
            $coup_coeur[$msg['sujet_id']] = $msg;
        unset($messages);

        // Lu - Non lu pour les requêtes du dessus
        if (verifier('connecte')) {
            $sids = array_merge(
                array_keys($last_posts),
                array_keys($last_topics),
                array_keys($coup_coeur));

            if (!empty($sids)) {
                $stmt = $dbh->prepare('SELECT lunonlu_sujet_id, '
                    . 'lunonlu_message_id, lunonlu_participe '
                    . 'FROM zcov2_forum_lunonlu '
                    . 'WHERE lunonlu_utilisateur_id = :id_user '
                    . ' AND lunonlu_sujet_id IN('
                    . implode(', ', $sids)
                    . ')');
                $stmt->bindParam(':id_user', $_SESSION['id']);
                $stmt->execute();
                while ($d = $stmt->fetch()) {
                    if (isset($last_posts[$d['lunonlu_sujet_id']]))
                        $last_posts[$d['lunonlu_sujet_id']] = array_merge(
                            $last_posts[$d['lunonlu_sujet_id']], $d);
                    if (isset($last_topics[$d['lunonlu_sujet_id']]))
                        $last_topics[$d['lunonlu_sujet_id']] = array_merge(
                            $last_topics[$d['lunonlu_sujet_id']], $d);
                    if (isset($coup_coeur[$d['lunonlu_sujet_id']]))
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
}