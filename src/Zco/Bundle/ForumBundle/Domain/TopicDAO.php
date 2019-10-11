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

use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class TopicDAO
{
    public static function InfosSujet($lesujet)
    {
        if (!verifier('connecte')) {
            $userid = 0;
        } else {
            $userid = $_SESSION['id'];
        }
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT sujet_id, sujet_titre, sujet_sous_titre, sujet_premier_message, sujet_dernier_message, sujet_auteur, sujet_ferme, sujet_annonce, sujet_sondage, sujet_resolu, sujet_corbeille, " .
            "sondage_question, sondage_ferme, sujet_forum_id, utilisateur_sexe," .
            "COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS sujet_auteur_pseudo, Ma.utilisateur_id_groupe AS sujet_auteur_groupe, " .
            "COUNT(*) AS nombre_de_messages, " .
            "lunonlu_utilisateur_id, lunonlu_message_id, vote_membre_id, " .
            "Na.message_date AS dernier_message_date, Na.message_auteur AS dernier_message_auteur " .
            "FROM zcov2_forum_sujets " .
            "LEFT JOIN zcov2_forum_messages ON zcov2_forum_sujets.sujet_id = zcov2_forum_messages.message_sujet_id " .
            "LEFT JOIN zcov2_forum_messages Na ON zcov2_forum_sujets.sujet_dernier_message = Na.message_id " .
            "LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id " .
            "LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_sujets.sujet_auteur = Ma.utilisateur_id " .
            "LEFT JOIN zcov2_forum_sondages ON zcov2_forum_sujets.sujet_sondage = zcov2_forum_sondages.sondage_id " .
            "LEFT JOIN zcov2_forum_sondages_votes ON " . $userid . " = zcov2_forum_sondages_votes.vote_membre_id AND zcov2_forum_sujets.sujet_sondage = zcov2_forum_sondages_votes.vote_sondage_id " .
            "LEFT JOIN zcov2_forum_lunonlu ON zcov2_forum_sujets.sujet_id = zcov2_forum_lunonlu.lunonlu_sujet_id AND " . $userid . " = zcov2_forum_lunonlu.lunonlu_utilisateur_id " .
            "WHERE sujet_id = :s " .
            "GROUP BY sujet_id");
        $stmt->bindParam(':s', $lesujet);

        $stmt->execute();

        $resultat = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ((!empty($resultat['sujet_id']) AND
            verifier('voir_sujets', $resultat['sujet_forum_id']) AND (!$resultat['sujet_corbeille'] OR verifier('corbeille_sujets', $resultat['sujet_forum_id'])))) {
            return $resultat;
        } else {
            return false;
        }
    }

    public static function ListerMessages($id, $PremierMess, $MessaAfficher)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT DISTINCT message_id, message_auteur, Ma.utilisateur_id_groupe AS auteur_groupe, Ma.utilisateur_sexe,
	message_texte, message_date, message_ip, message_help, groupe_nom, groupe_logo, groupe_logo_feminin, 
	Ma.utilisateur_nb_sanctions, Ma.utilisateur_forum_messages, Ma.utilisateur_pourcentage, Ma.utilisateur_site_web,
	message_date, message_sujet_id, message_edite_auteur, message_edite_date,
	sujet_date, Ma.utilisateur_citation, 
	COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS auteur_message_pseudo, Ma.utilisateur_avatar AS auteur_avatar,
	COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS auteur_edition_pseudo,
	Mb.utilisateur_id AS auteur_edition_id,
	Ma.utilisateur_signature AS auteur_message_signature, sujet_auteur, sujet_premier_message, sujet_dernier_message, 
	sujet_sondage, sujet_annonce, sujet_ferme
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_messages.message_auteur = Ma.utilisateur_id
	LEFT JOIN zcov2_utilisateurs Mb ON zcov2_forum_messages.message_edite_auteur = Mb.utilisateur_id
	LEFT JOIN zcov2_groupes ON Ma.utilisateur_id_groupe=groupe_id
	WHERE sujet_id = :s
	ORDER BY message_date ASC
	LIMIT " . $MessaAfficher . " OFFSET " . $PremierMess);

        $stmt->bindParam(':s', $id);
        $stmt->execute();


        return $stmt->fetchAll();
    }

    public static function TrouverLaPageDeCeMessage($id, $Message)
    {
        //Dès qu'il y a un paramètre "m" dans l'URL, cette fonction est appelée pour trouver sur quelle page du sujet se trouve le message concerné.
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare('SELECT message_id
	        FROM zcov2_forum_messages
	        WHERE message_sujet_id = :s
	        ORDER BY message_date ASC');
        $stmt->bindParam(':s', $id);

        $stmt->execute();
        while ($resultat0 = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultat[] = $resultat0;
        }

        //On calcule le nombre total de messages.
        $totalDesMessages = 0;
        foreach ($resultat as $clef => $valeur) {
            $totalDesMessages++;
        }

        //On calcule la position du message
        $arreter_boucle = false;
        $i = 1;
        $PositionDansLaPage = 1;
        $nbMessagesParPage = 20;
        foreach ($resultat as $clef => $valeur) {
            if (!$arreter_boucle) {
                if ($valeur["message_id"] != $Message) {
                    if ($PositionDansLaPage == $nbMessagesParPage) {
                        $PositionDansLaPage = 0;
                    }
                    $i++;
                    $PositionDansLaPage++;
                } else {
                    $arreter_boucle = true;
                }
            }
        }
        $nombreDePages = ceil($totalDesMessages / $nbMessagesParPage);
        $PageCible = ceil($i / $nbMessagesParPage);
        if ($PositionDansLaPage == $nbMessagesParPage AND $PageCible < $nombreDePages) {
            $PageCible++;
        }

        return $PageCible;
    }

    public static function RendreLeSujetLu($sujet_id, $page, $nombreDePages, $dernier_message, $ListerMessages, $InfosLuNonlu)
    {
        if (!empty($InfosLuNonlu['lunonlu_utilisateur_id'])) {
            $dejavu = true;
        } else {
            $dejavu = false;
        }

        $dbh = \Doctrine_Manager::connection()->getDbh();

        //Si on est sur la page la plus récente, on considère que le sujet entier est lu (jusqu'à son dernier message
        if ($page == $nombreDePages) {
            if (!$dejavu) {
                //Si c'est la première fois qu'on visite le sujet, on insère un nouvel enregistrement
                $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
			VALUES (:user_id, :sujet_id, :message_id, '0')");
                $stmt->bindParam(':user_id', $_SESSION['id']);
                $stmt->bindParam(':sujet_id', $sujet_id);
                $stmt->bindParam(':message_id', $dernier_message);

                $stmt->execute();

                $stmt->closeCursor();
            } else {
                if ($InfosLuNonlu['lunonlu_message_id'] != $dernier_message) {
                    //Sinon, on met simplement à jour si besoin (que si les deux valeurs diffèrent...).
                    $stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
				SET lunonlu_message_id = :message_id
				WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
                    $stmt->bindParam(':user_id', $_SESSION['id']);
                    $stmt->bindParam(':sujet_id', $sujet_id);
                    $stmt->bindParam(':message_id', $dernier_message);

                    $stmt->execute();

                    $stmt->closeCursor();
                }
            }
        } else {
            //Si on est sur une autre page que la plus récente, on considère que le sujet est lu jusqu'au dernier message s'affichant dans la page courante.
            //Donc on doit trouver le dernier message de la page courante...
            $i = 1;
            foreach ($ListerMessages as $clef => $valeur) {
                if ($i == 20) {
                    $MessageLePlusRecentDansLaPage = $valeur['message_id'];
                }
                $i++;
            }
            if (!isset($MessageLePlusRecentDansLaPage))
                $MessageLePlusRecentDansLaPage = $valeur['message_id'];

            //Ok, maintenant on a le dernier message de la page courante :)
            //On vérifie avant bien sûr que la mise à jour est nécessaire. Sinon on ne la fait pas :) La condition suivante nous économise quand-même une requête UPDATE quand elle est inutile ;)
            if ($InfosLuNonlu['lunonlu_message_id'] < $MessageLePlusRecentDansLaPage) {
                if (!$dejavu) {
                    //Si c'est la première fois qu'on visite le sujet, on insère un nouvel enregistrement
                    $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
				VALUES (:user_id, :sujet_id, :message_id, '0')");
                    $stmt->bindParam(':user_id', $_SESSION['id']);
                    $stmt->bindParam(':sujet_id', $sujet_id);
                    $stmt->bindParam(':message_id', $MessageLePlusRecentDansLaPage);

                    $stmt->execute();

                    $stmt->closeCursor();
                } else {

                    //Sinon, on met simplement à jour si besoin (que si les deux valeurs diffèrent...).
                    $stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
				SET lunonlu_message_id = :message_id
				WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
                    $stmt->bindParam(':user_id', $_SESSION['id']);
                    $stmt->bindParam(':sujet_id', $sujet_id);
                    $stmt->bindParam(':message_id', $MessageLePlusRecentDansLaPage);

                    $stmt->execute();

                    $stmt->closeCursor();
                }
            }
        }
        return true;
    }

    public static function RevueSujet($sujet_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT message_id, message_auteur, Ma.utilisateur_id_groupe AS auteur_groupe, message_texte, message_date, groupe_nom, groupe_logo,
	message_date, sujet_date, message_edite_date, message_sujet_id, message_edite_auteur,
	COALESCE(Ma.utilisateur_pseudo, 'Anonyme') AS auteur_message_pseudo, Ma.utilisateur_avatar AS auteur_avatar,
	COALESCE(Mb.utilisateur_pseudo, 'Anonyme') AS auteur_edition_pseudo, Mb.utilisateur_id AS auteur_edition_id,
	Ma.utilisateur_signature AS auteur_message_signature, Ma.utilisateur_citation, sujet_auteur, sujet_premier_message, sujet_dernier_message, sujet_sondage, sujet_annonce, sujet_ferme

	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_utilisateurs Ma ON zcov2_forum_messages.message_auteur = Ma.utilisateur_id
	LEFT JOIN zcov2_utilisateurs Mb ON zcov2_forum_messages.message_edite_auteur = Mb.utilisateur_id
	LEFT JOIN zcov2_groupes ON Ma.utilisateur_id_groupe=groupe_id
	WHERE sujet_id = :s
	ORDER BY message_date DESC
	LIMIT 15");

        $stmt->bindParam(':s', $sujet_id);

        $retour = array();
        if ($stmt->execute() && $retour[0] = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            while ($resultat = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $retour[] = $resultat;
            }
            return $retour;
        } else {
            return false;
        }
    }

    public static function EnregistrerNouveauSujet($id, $annonce, $ferme, $resolu, $corbeille)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // Création du sujet.
        $stmt = $dbh->prepare("INSERT INTO zcov2_forum_sujets (sujet_id, sujet_forum_id, sujet_titre, sujet_sous_titre, sujet_auteur, sujet_date, sujet_premier_message, sujet_dernier_message, sujet_sondage, sujet_annonce, sujet_ferme, sujet_resolu, sujet_corbeille)
	VALUES ('', :sujet_forum_id, :sujet_titre, :sujet_sous_titre, :sujet_auteur, NOW(), '', '', 0, :sujet_annonce, :sujet_ferme, :sujet_resolu, :sujet_corbeille)");
        $stmt->bindParam(':sujet_forum_id', $id);
        $stmt->bindParam(':sujet_titre', $_POST['titre']);
        $stmt->bindParam(':sujet_sous_titre', $_POST['sous_titre']);
        $stmt->bindParam(':sujet_auteur', $_SESSION['id']);
        $stmt->bindParam(':sujet_annonce', $annonce);
        $stmt->bindParam(':sujet_ferme', $ferme);
        $stmt->bindParam(':sujet_resolu', $resolu);
        $stmt->bindParam(':sujet_corbeille', $corbeille);
        $stmt->execute();
        $nouveau_sujet_id = $dbh->lastInsertId();
        $stmt->closeCursor();

        // Création du premier message.
        $stmt = $dbh->prepare("INSERT INTO zcov2_forum_messages (message_id, message_auteur, message_texte, message_date, message_sujet_id, message_edite_auteur, message_edite_date, message_ip)
	VALUES ('', :message_auteur, :message_texte, NOW(), :message_sujet_id, 0, '', :ip)");
        $stmt->bindParam(':message_auteur', $_SESSION['id']);
        $stmt->bindParam(':message_texte', $_POST['texte']);
        $stmt->bindParam(':message_sujet_id', $nouveau_sujet_id);
        $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
        $stmt->execute();
        $nouveau_message_id = $dbh->lastInsertId();
        $stmt->closeCursor();

        // Grâce au numéro du post récupéré, on peut updater la table des sujets pour indiquer que ce post est le premier et le dernier du sujet.
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :sujet_premier_message, sujet_dernier_message = :sujet_dernier_message
	WHERE sujet_id = :nouveau_sujet_id");
        $stmt->bindParam(':sujet_premier_message', $nouveau_message_id);
        $stmt->bindParam(':sujet_dernier_message', $nouveau_message_id);
        $stmt->bindParam(':nouveau_sujet_id', $nouveau_sujet_id);
        $stmt->execute();
        $stmt->closeCursor();

        if (!$corbeille) {
            //Enfin, on met à jour la table forums : on met à jour le dernier message posté du forum.
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $nouveau_message_id);
            $stmt->bindParam(':forum_id', $_GET['id']);
            $stmt->execute();

            $stmt->closeCursor();
        }

        //Puis on crée l'enregistrement pour la table lu / nonlu
        $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
	VALUES (:user_id, :sujet_id, :message_id, '1')");
        $stmt->bindParam(':user_id', $_SESSION['id']);
        $stmt->bindParam(':sujet_id', $nouveau_sujet_id);
        $stmt->bindParam(':message_id', $nouveau_message_id);

        $stmt->execute();

        $stmt->closeCursor();

        if (!$corbeille) {
            //Enfin, on incrémente le nombre de messages du membre :)
            $stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages+1
		WHERE utilisateur_id = :utilisateur_id");
            $stmt->bindParam(':utilisateur_id', $_SESSION['id']);
            $stmt->execute();


            $stmt->closeCursor();
        }
        return $nouveau_sujet_id;
    }

    /**
     * Vérifie si un sujet est dans un forum archivé ou non.
     *
     * @param        int $id ID du sujet.
     * @return        bool
     */
    public static function sujetIsArchive($id)
    {
        $InfosSujet = self::InfosSujet($id);
        $InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);

        return ($InfosForum['cat_archive'] == 1) ? (true) : (false);
    }

    /**
     * Change le statut résolu d'un sujet.
     * @param integer $sujet_id L'id du sujet.
     * @param boolean $resolu_actuel Le statut résolu actuel.
     * @return void
     */
    public static function ChangerResoluSujet($sujet_id, $resolu_actuel)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($resolu_actuel) {
            //Si le sujet est résolu, on le met normal.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_resolu = 0
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        } else {
            //Si c'est un sujet normal, on le met en résolu.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_resolu = 1
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        }
    }

    /**
     * Change le statut d'annonce (ou pas) d'un sujet.
     * @param integer $sujet_id L'id du sujet concerné.
     * @param boolean $type_actuel Son statut actuel.
     */
    public static function ChangerTypeSujet($sujet_id, $type_actuel)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($type_actuel) {
            //Si c'est une annonce, on le met en normal.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_annonce = 0
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        } else {
            //Si c'est un sujet normal, on le met en annonce.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_annonce = 1
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        }
    }

    /**
     * Change le statut fermé (ou pas) d'un sujet.
     * @param integer $sujet_id L'id du sujet concerné.
     * @param boolean $statut_actuel Son statut actuel.
     */
    public static function ChangerStatutSujet($sujet_id, $statut_actuel)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($statut_actuel) {
            //Si le sujet est fermé, on l'ouvre.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_ferme = 0
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        } else {
            //Si le sujet est ouvert, on le ferme.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_ferme = 1
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        }
    }

    /**
     * Déplace un sujet de forum.
     * @param integer $id_suj L'id du sujet à déplacer.
     * @param integer $forum_source L'id du forum du sujet.
     * @param integer $forum_cible L'id du forum ciblé.
     * @return void
     */
    public static function DeplacerSujet($id_suj, $forum_source, $forum_cible)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //On met à jour le sujet (on le change de forum).
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_forum_id = :forum_cible
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_id', $id_suj);
        $stmt->bindParam(':forum_cible', $forum_cible);
        $stmt->execute();
        $stmt->closeCursor();

        //On recherche le dernier message du forum source.
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_source AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':forum_source', $forum_source);
        $stmt->execute();
        $FofoSource = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($FofoSource['message_id'])) {
            $FofoSource['message_id'] = 0;
        }

        //Maintenant qu'on a le dernier message du forum source, on update.
        $stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_source");
        $stmt->bindParam(':forum_dernier_post_id', $FofoSource['message_id']);
        $stmt->bindParam(':forum_source', $forum_source);
        $stmt->execute();
        $stmt->closeCursor();

        //On recherche le dernier message du forum cible.
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_cible AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':forum_cible', $forum_cible);
        $stmt->execute();
        $FofoCible = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($FofoCible['message_id'])) {
            $FofoCible['message_id'] = 0;
        }

        //Maintenant qu'on a le dernier message du forum cible, on update.
        $stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_cible");
        $stmt->bindParam(':forum_dernier_post_id', $FofoCible['message_id']);
        $stmt->bindParam(':forum_cible', $forum_cible);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Supprime un sujet.
     * @param integer $sujet_id L'id du sujet.
     * @param integer $forum_id L'id de son forum.
     * @param boolean $corbeille Est-il en corbeille ?
     * @param integer|null $sujet_sondage L'id du sondage associé.
     * @return void
     */
    public static function Supprimer($sujet_id, $forum_id, $corbeille, $sujet_sondage = null)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        if (is_null($sujet_sondage)) {
            $stmt = $dbh->prepare("SELECT sujet_sondage FROM zcov2_forum_sujets WHERE sujet_id = :id");
            $stmt->bindParam(':id', $sujet_id);

            $stmt->execute();

            $sujet_sondage = $stmt->fetchColumn();
            $stmt->closeCursor();
        }

        //https://openclassrooms.com/forum/sujet/phpsql-update-avec-un-array-42448#r1553415

        if (!$corbeille) {
            //On calcule le nombre de messages postés par membre, mais uniquement les messages postés dans le sujet à supprimer.
            $stmt = $dbh->prepare("
		SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :sujet_id
		GROUP BY message_auteur
		");
            $stmt->bindParam(':sujet_id', $sujet_id);

            $NombreMessageDesPosteursDansSujet = array();
            if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                while ($resultat = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $NombreMessageDesPosteursDansSujet[] = $resultat;
                }
            }

            $stmt->closeCursor();

            //On va voir ici tout l'avantage des requêtes préparées !
            //On met à jour le nombre de messages par membres...

            $stmt = $dbh->prepare("
		UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages - :nombre_a_enlever
		WHERE utilisateur_id = :message_auteur
		");

            foreach ($NombreMessageDesPosteursDansSujet as $clef => &$valeur) {
                $stmt->bindParam(':nombre_a_enlever', $valeur['NombreMessageDesPosteursDansSujet']);
                $stmt->bindParam(':message_auteur', $valeur['message_auteur']);

                $stmt->execute();
            }
            $stmt->closeCursor();
        }

        //On supprime le sujet.
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_sujets
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();

        //On supprime les messages du sujet.
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id");
        $stmt->bindParam(':message_sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();

        //On supprime les alertes du sujet.
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_alertes
	WHERE alerte_sujet_id = :message_sujet_id");
        $stmt->bindParam(':message_sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();

        ################ DÉBUT sondage ################
        //On supprime le sondage du sujet.
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages
	WHERE sondage_id = :sondage_id");
        $stmt->bindParam(':sondage_id', $sujet_sondage);
        $stmt->execute();
        $stmt->closeCursor();

        //On supprime les choix du sondage
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_choix
	WHERE choix_sondage_id = :choix_sondage_id");
        $stmt->bindParam(':choix_sondage_id', $sujet_sondage);
        $stmt->execute();
        $stmt->closeCursor();

        //On supprime les votes du sondage
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_sondages_votes
	WHERE vote_sondage_id = :vote_sondage_id");
        $stmt->bindParam(':vote_sondage_id', $sujet_sondage);
        $stmt->execute();
        $stmt->closeCursor();
        ################ FIN sondage ################

        //On supprime les enregistrements de la table lu / non-lu concernant ce sujet.
        //Ils ne dérangent pas mais ils ne servent plus à rien. Donc autant les supprimer !
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_lunonlu
	WHERE lunonlu_sujet_id = :lunonlu_sujet_id");
        $stmt->bindParam(':lunonlu_sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();

        //On recherche le dernier message du forum.
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = :zero
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->bindValue(':zero', 0);
        $stmt->execute();
        $Fofo = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if (empty($Fofo['message_id'])) {
            $Fofo['message_id'] = 0;
        }

        //Maintenant qu'on a le dernier message du forum, on update.
        $stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
        $stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public static function SupprimerMessage($message_id, $sujet_id, $sujet_dernier_message, $forum_id, $corbeille)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        if (!$corbeille) {
            //On cherche l'auteur du message
            $stmt = $dbh->prepare("SELECT message_auteur
		FROM zcov2_forum_messages
		WHERE message_id = :message_id");
            $stmt->bindParam(':message_id', $message_id);

            $stmt->execute();
            $AuteurMessage = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            //On décrémente le nombre de messages du membre :)
            $stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages-1
		WHERE utilisateur_id = :utilisateur_id");
            $stmt->bindParam(':utilisateur_id', $AuteurMessage['message_auteur']);
            $stmt->execute();

            $stmt->closeCursor();
        }

        //On supprime le message.
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_messages
	WHERE message_id = :message_id");
        $stmt->bindParam(':message_id', $message_id);

        $stmt->execute();

        $stmt->closeCursor();

        //Il faut vérifier si dans la base de données si le message qu'on supprime était le dernier message vu par quelqu'un.
        //On récupère juste un tableau ici. Les updates se font en fin de fonction.
        $stmt = $dbh->prepare("SELECT lunonlu_utilisateur_id FROM zcov2_forum_lunonlu
	WHERE lunonlu_message_id = :lunonlu_message_id");
        $stmt->bindParam(':lunonlu_message_id', $message_id);

        $stmt->execute();
        if ($MettreAjourDernierMessageVu[0] = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            while ($resultat = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $MettreAjourDernierMessageVu[] = $resultat;
            }
        }

        $stmt->closeCursor();

        if ($message_id == $sujet_dernier_message) {
            //On récupère le dernier message du sujet (il a changé vu qu'on vient de supprimer un message...)
            $stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :message_sujet_id
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
            $stmt->bindParam(':message_sujet_id', $sujet_id);

            $stmt->execute();
            $DernierMessSujet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            //On update la table du sujet, pour indiquer le dernier message du sujet (qu'on vient de récupérer au-dessus) et pour décrémenter le nombre de réponses.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_dernier_message = :sujet_dernier_message,
		sujet_reponses = sujet_reponses-1
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_dernier_message', $DernierMessSujet['message_id']);
            $stmt->bindParam(':sujet_id', $sujet_id);

            $stmt->execute();

            $stmt->closeCursor();

            //On recherche le dernier message du forum.
            $stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = :zero
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
            $stmt->bindParam(':forum_id', $forum_id);
            $stmt->bindValue(':zero', 0);

            $stmt->execute();
            $Fofo = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            if (empty($Fofo['message_id'])) {
                $Fofo['message_id'] = 0;
            }

            //Maintenant qu'on a le dernier message du forum, on update.
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
            $stmt->bindParam(':forum_id', $forum_id);

            $stmt->execute();

            $stmt->closeCursor();
        } else {
            //On update la table du sujet, pour décrémenter le nombre de réponses.
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET sujet_reponses = sujet_reponses-1
		WHERE sujet_id = :sujet_id");
            $stmt->bindParam(':sujet_id', $sujet_id);

            $stmt->execute();

            $stmt->closeCursor();
        }

        if (!empty($MettreAjourDernierMessageVu[0]['lunonlu_utilisateur_id']) AND $message_id != $sujet_dernier_message) {
            //On récupère le dernier message du sujet (il a changé vu qu'on vient de supprimer un message...)
            $stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		WHERE message_sujet_id = :message_sujet_id
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
            $stmt->bindParam(':message_sujet_id', $sujet_id);

            $stmt->execute();
            $DernierMessSujet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $stmt->closeCursor();
        }

        if (!empty($MettreAjourDernierMessageVu[0]['lunonlu_utilisateur_id'])) {
            //Voilà, c'est ici qu'on update le système lu /nonlu (voir deuxième requête de cette fonction => tout en haut).
            $LeBindParam = '';
            foreach ($MettreAjourDernierMessageVu as $clef => $valeur) {
                $LeBindParam .= $MettreAjourDernierMessageVu[$clef]['lunonlu_utilisateur_id'] . ',';
            }
            $LeBindParam = substr($LeBindParam, 0, -1);

            $stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
		SET lunonlu_message_id = :lunonlu_message_id
		WHERE lunonlu_utilisateur_id IN(:lunonlu_utilisateur_id) AND lunonlu_sujet_id = :lunonlu_sujet_id");
            $stmt->bindParam(':lunonlu_message_id', $DernierMessSujet['message_id']);
            $stmt->bindParam(':lunonlu_utilisateur_id', $LeBindParam);
            $stmt->bindParam(':lunonlu_sujet_id', $sujet_id);

            $stmt->execute();

            $stmt->closeCursor();
        }
    }

    /**
     * Jette un sujet à la corbeille.
     * @param integer $sujet_id L'id du sujet.
     * @param integer $forum_id L'id du forum dans lequel est le sujet.
     * @return void
     */
    public static function Corbeille($sujet_id, $forum_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //http://www.siteduzero.com/forum-83-168652-1553415.html#r1553415

        //On calcule le nombre de messages postés par membre, mais uniquement les
        //messages postés dans le sujet à mettre en corbeille.
        $stmt = $dbh->prepare("
	SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :sujet_id
	GROUP BY message_auteur
	");
        $stmt->bindParam(':sujet_id', $sujet_id);

        $NombreMessageDesPosteursDansSujet = array();
        if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            while ($resultat = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $NombreMessageDesPosteursDansSujet[] = $resultat;
            }
        }
        $stmt->closeCursor();

        //On va voir ici tout l'avantage des requêtes préparées !
        //On met à jour le nombre de messages par membres...
        $stmt = $dbh->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_forum_messages = utilisateur_forum_messages - :nombre_a_enlever
	WHERE utilisateur_id = :message_auteur
	");

        foreach ($NombreMessageDesPosteursDansSujet as $clef => &$valeur) {
            $stmt->bindParam(':nombre_a_enlever', $valeur['NombreMessageDesPosteursDansSujet']);
            $stmt->bindParam(':message_auteur', $valeur['message_auteur']);
            $stmt->execute();
        }
        $stmt->closeCursor();

        //On met le sujet en corbeille.
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_corbeille = 1
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();


        //On recherche le dernier message du forum.
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->execute();
        $Fofo = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($Fofo['message_id'])) {
            $Fofo['message_id'] = 0;
        }

        //Maintenant qu'on a le dernier message du forum, on update.
        $stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
        $stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Sort un sujet de la corbeille.
     * @param integer $sujet_id L'id du sujet.
     * @param integer $forum_id L'id du forum dans lequel est le sujet.
     * @return void
     */
    public static function Restaurer($sujet_id, $forum_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //http://www.siteduzero.com/forum-83-168652-1553415.html#r1553415

        //On calcule le nombre de messages postés par membre, mais uniquement les
        //messages postés dans le sujet à restaurer.
        $stmt = $dbh->prepare("
	SELECT DISTINCT message_auteur, COUNT( message_id ) AS NombreMessageDesPosteursDansSujet
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :sujet_id
	GROUP BY message_auteur
	");
        $stmt->bindParam(':sujet_id', $sujet_id);

        $NombreMessageDesPosteursDansSujet = array();
        if ($stmt->execute() && $NombreMessageDesPosteursDansSujet[0] = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            while ($resultat = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $NombreMessageDesPosteursDansSujet[] = $resultat;
            }
        }
        $stmt->closeCursor();

        //On va voir ici tout l'avantage des requêtes préparées !
        //On met à jour le nombre de messages par membres...
        $stmt = $dbh->prepare("
	UPDATE zcov2_utilisateurs
	SET utilisateur_forum_messages = utilisateur_forum_messages + :nombre_a_ajouter
	WHERE utilisateur_id = :message_auteur
	");

        foreach ($NombreMessageDesPosteursDansSujet as $clef => &$valeur) {
            $stmt->bindParam(':nombre_a_ajouter', $valeur['NombreMessageDesPosteursDansSujet']);
            $stmt->bindParam(':message_auteur', $valeur['message_auteur']);
            $stmt->execute();
        }
        $stmt->closeCursor();

        //On restaure le sujet.
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_corbeille = 0
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_id', $sujet_id);
        $stmt->execute();
        $stmt->closeCursor();


        //On recherche le dernier message du forum.
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
	WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->execute();
        $Fofo = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($Fofo['message_id'])) {
            $Fofo['message_id'] = 0;
        }

        //Maintenant qu'on a le dernier message du forum, on update.
        $stmt = $dbh->prepare("UPDATE zcov2_categories
	SET cat_last_element = :forum_dernier_post_id
	WHERE cat_id = :forum_id");
        $stmt->bindParam(':forum_dernier_post_id', $Fofo['message_id']);
        $stmt->bindParam(':forum_id', $forum_id);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public static function DiviserSujet($infos, $corbeille)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //On supprime tous les enregistrements de la tables des lu / non-lus
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_lunonlu WHERE lunonlu_sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_id', $infos['sujet_id']);

        $stmt->execute();

        $stmt->closeCursor();


        //On crée le nouveau sujet
        //--- On récupère ldes infos sur les messages à déplacer
        $in = array();
        foreach ($_POST['msg'] as $cle => $valeur)
            $in[] = $cle;

        $stmt = $dbh->prepare("SELECT message_id, message_auteur
	FROM zcov2_forum_messages
	WHERE message_id IN(" . implode(', ', $in) . ")
	ORDER BY message_date ASC");

        $stmt->execute();

        $messages = $stmt->fetchAll();
        $stmt->closeCursor();

        //--- Insertion en BDD
        $stmt = $dbh->prepare("INSERT INTO zcov2_forum_sujets (sujet_forum_id, sujet_titre, sujet_sous_titre, sujet_auteur, sujet_date, sujet_premier_message, sujet_dernier_message, sujet_annonce, sujet_ferme, sujet_resolu, sujet_corbeille, sujet_reponses)
	VALUES (:sujet_forum_id, :sujet_titre, :sujet_sous_titre, :sujet_auteur, NOW(), :premier_message, :dernier_message, 0, 0, 0, 0, :reponses)");
        $stmt->bindParam(':sujet_forum_id', $_POST['forum']);
        $stmt->bindParam(':sujet_titre', $_POST['titre']);
        $stmt->bindParam(':sujet_sous_titre', $_POST['sous_titre']);
        $stmt->bindParam(':sujet_auteur', $messages[0]['message_auteur']);
        $stmt->bindParam(':premier_message', $messages[0]['message_id']);
        $stmt->bindParam(':dernier_message', $messages[(count($messages) - 1)]['message_id']);
        $stmt->bindValue(':reponses', (count($messages) - 1));

        $stmt->execute();


        //--- On récupère l'id de l'enregistrement qui vient d'être créé (l'id du nouveau sujet).
        $nouveau_sujet_id = $dbh->lastInsertId();
        $stmt->closeCursor();

        //-- On déplace les posts
        $stmt = $dbh->prepare("
	UPDATE zcov2_forum_messages
	SET message_sujet_id = :id_sujet
	WHERE message_id IN(" . implode(', ', $in) . ")");
        $stmt->bindParam(':id_sujet', $nouveau_sujet_id);

        $stmt->execute();

        $stmt->closeCursor();

        //--- Puis on ajoute des enregistrements de la table lu / nonlu
        foreach ($messages as $m) {
            $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
		VALUES (:user_id, :sujet_id, :message_id, '1')
		ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
            $stmt->bindParam(':user_id', $m['message_auteur']);
            $stmt->bindParam(':sujet_id', $nouveau_sujet_id);
            $stmt->bindParam(':message_id', $m['message_id']);

            $stmt->execute();

            $stmt->closeCursor();
        }

        //--- Si on n'est pas dans la corbeille, il faut penser à mettre à jour le dernier message posté du forum
        if (!$corbeille) {
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $messages[(count($messages) - 1)]['message_id']);
            $stmt->bindParam(':forum_id', $_POST['forum']);

            $stmt->execute();

            $stmt->closeCursor();
        }


        //Enfin on s'occupe de l'ancien sujet
        //--- On récupère le premier message du sujet
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date ASC, message_id ASC
	LIMIT 0, 1");
        $stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

        $stmt->execute();
        $PremierMessSujet = $stmt->fetchColumn();

        $stmt->closeCursor();

        //--- On récupère le dernier message du sujet
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

        $stmt->execute();
        $DernierMessSujet = $stmt->fetchColumn();

        $stmt->closeCursor();

        //--- On update la table du sujet, pour indiquer le premier et le dernier message du sujet, et pour décrémenter le nombre de réponses
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :premier_message,  sujet_dernier_message = :dernier_message, sujet_reponses = sujet_reponses - :nb
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':premier_message', $PremierMessSujet);
        $stmt->bindParam(':dernier_message', $DernierMessSujet);
        $stmt->bindValue(':nb', count($messages));
        $stmt->bindParam(':sujet_id', $infos['sujet_id']);

        $stmt->execute();

        $stmt->closeCursor();

        //--- Puis on met à jour les enregistrements de la table lu / nonlu
        $ListerMessages = self::ListerMessages($infos['sujet_id'], 0, $infos['nombre_de_messages']);
        foreach ($ListerMessages as $m) {
            //Si le message ne doit pas être supprimé
            if (!in_array($m['message_id'], $in)) {
                $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
			VALUES (:user_id, :sujet_id, :message_id, '1')
			ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
                $stmt->bindParam(':user_id', $m['message_auteur']);
                $stmt->bindParam(':sujet_id', $infos['sujet_id']);
                $stmt->bindParam(':message_id', $m['message_id']);

                $stmt->execute();

                $stmt->closeCursor();
            }
        }

        if (!$corbeille) {
            //--- On recherche le dernier message du forum.
            $stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
            $stmt->bindParam(':forum_id', $infos['forum_id']);

            $stmt->execute();
            $Fofo = $stmt->fetchColumn();

            $stmt->closeCursor();

            //--- Maintenant qu'on a le dernier message du forum, on update.
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $Fofo);
            $stmt->bindParam(':forum_id', $infos['forum_id']);

            $stmt->execute();

            $stmt->closeCursor();
        }
    }

    public static function FusionnerSujets($infos, $corbeille)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $in = array();

        foreach ($_POST['sujet'] as $cle => $valeur) {
            $in[] = $cle;
        }

        //On récupère des infos sur les sujets
        $stmt = $dbh->prepare("SELECT sujet_id, sujet_reponses, sujet_forum_id, sujet_corbeille, sujet_sondage
	FROM zcov2_forum_sujets
	WHERE sujet_id IN(" . implode(', ', $in) . ")");
        $stmt->execute();
        $sujets = $stmt->fetchAll();

        $stmt->closeCursor();

        //On change les messages de sujet
        $stmt = $dbh->prepare("
	UPDATE zcov2_forum_messages
	SET message_sujet_id = :new
	WHERE message_sujet_id IN(" . implode(', ', $in) . ")");
        $stmt->bindParam(':new', $infos['sujet_id']);

        $stmt->execute();

        $stmt->closeCursor();

        //On calcule le nombre de messages à ajouter
        $nb_messages = 0;
        foreach ($sujets as $s) {
            if ($s['sujet_id'] != $infos['sujet_id'])
                $nb_messages += ($s['sujet_reponses'] + 1);
        }

        //Mise à jour du sujet parent
        //--- On récupère le premier message du sujet
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date ASC, message_id ASC
	LIMIT 0, 1");
        $stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

        $stmt->execute();
        $PremierMessSujet = $stmt->fetchColumn();

        $stmt->closeCursor();

        //--- On récupère le dernier message du sujet
        $stmt = $dbh->prepare("SELECT message_id
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :message_sujet_id
	ORDER BY message_date DESC, message_id DESC
	LIMIT 0, 1");
        $stmt->bindParam(':message_sujet_id', $infos['sujet_id']);

        $stmt->execute();
        $DernierMessSujet = $stmt->fetchColumn();

        $stmt->closeCursor();

        //--- On update la table du sujet, pour indiquer le premier et le dernier message du sujet, et pour augmenter le nombre de réponses
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_premier_message = :premier_message,  sujet_dernier_message = :dernier_message, sujet_reponses = sujet_reponses + :nb
	WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':premier_message', $PremierMessSujet);
        $stmt->bindParam(':dernier_message', $DernierMessSujet);
        $stmt->bindParam(':nb', $nb_messages);
        $stmt->bindParam(':sujet_id', $infos['sujet_id']);

        $stmt->execute();

        $stmt->closeCursor();

        //On supprime les anciens sujets (les messages ont déjà été déplacés, ils ne seront pas supprimés)
        foreach ($sujets as $s) {
            if ($s['sujet_id'] != $infos['sujet_id'])
                self::Supprimer($s['sujet_id'], $s['sujet_forum_id'], $s['sujet_corbeille'], $s['sujet_sondage']);
        }

        //Mise à jour pour le forum
        if (!$corbeille) {
            //--- On recherche le dernier message du forum.
            $stmt = $dbh->prepare("SELECT message_id
		FROM zcov2_forum_messages
		LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
		LEFT JOIN zcov2_categories ON sujet_forum_id = cat_id
		WHERE sujet_forum_id = :forum_id AND sujet_corbeille = 0
		ORDER BY message_date DESC, message_id DESC
		LIMIT 0, 1");
            $stmt->bindParam(':forum_id', $infos['forum_id']);

            $stmt->execute();
            $Fofo = $stmt->fetchColumn();

            $stmt->closeCursor();

            //--- Maintenant qu'on a le dernier message du forum, on update.
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $Fofo);
            $stmt->bindParam(':forum_id', $infos['forum_id']);

            $stmt->execute();

            $stmt->closeCursor();
        }

        //Mise à jour pour les lus / non-lus
        $stmt = $dbh->prepare("SELECT message_id, message_auteur
	FROM zcov2_forum_messages
	WHERE message_sujet_id = :id");
        $stmt->bindParam(':id', $infos['sujet_id']);

        $stmt->execute();

        $messages = $stmt->fetchAll();
        $stmt->closeCursor();

        foreach ($messages as $m) {
            $stmt = $dbh->prepare("INSERT INTO zcov2_forum_lunonlu (lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id, lunonlu_participe)
		VALUES (:user_id, :sujet_id, :message_id, '1')
		ON DUPLICATE KEY UPDATE lunonlu_message_id = :message_id");
            $stmt->bindParam(':user_id', $m['message_auteur']);
            $stmt->bindParam(':sujet_id', $infos['sujet_id']);
            $stmt->bindParam(':message_id', $m['message_id']);

            $stmt->execute();

            $stmt->closeCursor();
        }
    }
}