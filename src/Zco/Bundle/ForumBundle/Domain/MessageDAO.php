<?php


namespace Zco\Bundle\ForumBundle\Domain;

/**
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
final class MessageDAO
{
    /**
     * Change le statut du message (a aidé ou non).
     * @param integer $message_id L'id du message.
     * @param integer $help_souhaite A-t-il aidé ou pas ?
     * @return void
     */
    public static function ChangerHelp($message_id, $help_souhaite)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($help_souhaite) {
            $stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_help = 1
		WHERE message_id = :message_id");
            $stmt->bindParam(':message_id', $message_id);
            $stmt->execute();
        } else {
            $stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_help = 0
		WHERE message_id = :message_id");
            $stmt->bindParam(':message_id', $message_id);
            $stmt->execute();
        }
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
        $stmt = $dbh->prepare("DELETE FROM zcov2_forum_messages WHERE message_id = :message_id");
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

    public static function VerifierValiditeMessage($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // Vérification de l'existence du message, et vérification des droits de lecture et d'édition (d'écriture) pour ce message.
        $stmt = $dbh->prepare("
	SELECT message_id, message_sujet_id, sujet_ferme, sujet_forum_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	WHERE message_id = :m");
        $stmt->bindParam(':m', $id);

        $stmt->execute();

        $resultat = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!empty($resultat['message_id']) AND verifier('voir_sujets', $resultat['sujet_forum_id'])) {
            // Si le message existe et que l'utilisateur a le droit de voir le message, on vérifie si le sujet est fermé.
            if ($resultat['message_sujet_id'] != $_GET['id']) {
                return false;
            } elseif ($resultat['sujet_ferme'] AND verifier('corbeille_sujets', $resultat['forum_id'])) {
                return true;
            } elseif ($resultat['sujet_ferme'] AND !verifier('corbeille_sujets', $resultat['forum_id'])) {
                return false;
            } elseif (!$resultat['sujet_ferme']) {
                return true;
            }
        }

        return false;
    }

    public static function InfosMessage($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT message_id, message_auteur, message_texte, message_sujet_id, sujet_auteur, message_date, utilisateur_pseudo, utilisateur_sexe,
	sujet_id, sujet_titre, sujet_sous_titre, sujet_premier_message, sujet_dernier_message, sujet_ferme, sujet_resolu, sujet_annonce, sujet_corbeille, 
	sujet_sondage, sondage_question, sujet_forum_id, sujet_auteur,
	lunonlu_utilisateur_id
        FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON zcov2_forum_messages.message_sujet_id = zcov2_forum_sujets.sujet_id
	LEFT JOIN zcov2_utilisateurs ON message_auteur = utilisateur_id
        LEFT JOIN zcov2_forum_lunonlu ON
            message_sujet_id = zcov2_forum_lunonlu.lunonlu_sujet_id AND
            zcov2_forum_lunonlu.lunonlu_utilisateur_id = :utilisateur
	LEFT JOIN zcov2_forum_sondages ON zcov2_forum_sujets.sujet_sondage = zcov2_forum_sondages.sondage_id
	WHERE message_id = :m
	");
        $stmt->bindParam(':m', $id);
        $stmt->bindParam(':utilisateur', $_SESSION['id']);

        $stmt->execute();
        $resultat = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!empty($resultat['message_id']) AND verifier('voir_sujets', $resultat['sujet_forum_id']) AND !$resultat['sujet_corbeille']) {
            // Si le message existe et que l'utilisateur a le droit de voir le message, on vérifie si le sujet est fermé.
            if ($resultat['sujet_ferme'] AND verifier('repondre_sujets_fermes', $resultat['sujet_forum_id'])) {
                return $resultat;
            } elseif ($resultat['sujet_ferme'] AND !verifier('repondre_sujets_fermes', $resultat['sujet_forum_id'])) {
                return false;
            } elseif (!$resultat['sujet_ferme']) {
                return $resultat;
            }
        }

        return false;
    }

    public static function EnregistrerNouveauMessage($id, $forum_id, $corbeille)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // On crée le post
        $stmt = $dbh->prepare("INSERT INTO zcov2_forum_messages (" .
            "message_auteur, message_texte, message_date, message_sujet_id, " .
            "message_ip) " .
            "VALUES (:message_auteur, :message_texte, NOW(), :message_sujet_id, :ip)");
        $stmt->bindParam(':message_auteur', $_SESSION['id']);
        $stmt->bindParam(':message_texte', $_POST['texte']);
        $stmt->bindParam(':message_sujet_id', $id);
        $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
        $stmt->execute();

        // On récupère l'id de l'enregistrement qui vient d'être créé (l'id du nouveau post).
        $nouveau_message_id = $dbh->lastInsertId();

        // Grâce au numéro du post récupéré, on peut updater la table des sujets
        // pour indiquer que ce post est le dernier du sujet, et pour incrémenter
        // le nombre de réponses, et pour changer (ou pas) le type, le statut du
        // sujet, sa résolution.
        $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets " .
            "SET sujet_dernier_message = :sujet_dernier_message, " .
            "sujet_reponses = sujet_reponses+1 " .
            "WHERE sujet_id = :sujet_id");
        $stmt->bindParam(':sujet_dernier_message', $nouveau_message_id);
        $stmt->bindParam(':sujet_id', $id);
        $stmt->execute();

        // Puis on met à jour la table lu / nonlu
        $stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
	SET lunonlu_message_id = :message_id, lunonlu_participe = 1
	WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
        $stmt->bindParam(':user_id', $_SESSION['id']);
        $stmt->bindParam(':sujet_id', $id);
        $stmt->bindParam(':message_id', $nouveau_message_id);

        $stmt->execute();

        if (!$corbeille) {
            // Enfin, on met à jour la table forums : on met à jour le dernier message posté du forum.
            $stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
            $stmt->bindParam(':forum_dernier_post_id', $nouveau_message_id);
            $stmt->bindParam(':forum_id', $forum_id);
            $stmt->execute();

            // Enfin, on incrémente le nombre de messages du membre :)
            $stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages+1
		WHERE utilisateur_id = :utilisateur_id");
            $stmt->bindParam(':utilisateur_id', $_SESSION['id']);
            $stmt->execute();
        }

        return $nouveau_message_id;
    }


    public static function EditerMessage($id, $forum_id, $sujet_id, $annonce, $ferme, $resolu, $sujet_auteur)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // Affichage de la notification d'édition ou non
        if (isset($_POST['aucun_message_edit']) AND verifier('masquer_avis_edition', $forum_id)) {
            // On modifie le message sans notification d'édition
            $stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_texte = :message_texte, message_edite_auteur = '', message_edite_date = ''
		WHERE message_id = :m");
            $stmt->bindParam(':message_texte', $_POST['texte']);
            $stmt->bindParam(':m', $id);
            $stmt->execute();
        } else {
            // On modifie le message
            $stmt = $dbh->prepare("UPDATE zcov2_forum_messages
		SET message_texte = :message_texte, message_edite_auteur = :message_edite_auteur, message_edite_date = NOW()
		WHERE message_id = :m");
            $stmt->bindParam(':message_texte', $_POST['texte']);
            $stmt->bindParam(':m', $id);
            $stmt->bindParam(':message_edite_auteur', $_SESSION['id']);
            $stmt->execute();
        }

        $add = '';
        if (verifier('epingler_sujets', $forum_id)) {
            $add .= 'sujet_annonce = :sujet_annonce, ';
        }
        if (verifier('fermer_sujets', $forum_id)) {
            $add .= 'sujet_ferme = :sujet_ferme, ';
        }
        if (verifier('resolu_sujets', $forum_id)) {
            $add .= 'sujet_resolu = :sujet_resolu, ';
        }
        if ((verifier('editer_sujets', $forum_id) || (verifier('editer_ses_sujets', $forum_id) && $_SESSION['id'] == $sujet_auteur)) && !empty($_POST['titre'])) {
            $add .= 'sujet_titre = :sujet_titre, sujet_sous_titre = :sujet_sous_titre, ';
        }
        if (!empty($add)) {
            $add = substr($add, 0, -2);

            // On update le sujet
            $stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
		SET " . $add . "
		WHERE sujet_id = :sujet_id");
            if (verifier('epingler_sujets', $forum_id)) {
                $stmt->bindParam(':sujet_annonce', $annonce);
            }
            if (verifier('fermer_sujets', $forum_id)) {
                $stmt->bindParam(':sujet_ferme', $ferme);
            }
            if (verifier('resolu_sujets', $forum_id)) {
                $stmt->bindParam(':sujet_resolu', $resolu);
            }
            if ((verifier('editer_sujets', $forum_id) || verifier('editer_ses_sujets', $forum_id) && $_SESSION['id'] == $sujet_auteur) && !empty($_POST['titre'])) {
                $stmt->bindParam(':sujet_titre', $_POST['titre']);
                $stmt->bindParam(':sujet_sous_titre', $_POST['sous_titre']);
            }
            $stmt->bindParam(':sujet_id', $sujet_id);
            $stmt->execute();
        }

        return true;
    }

    public static function ListerMessagesId($id, $page = null, $nb_resultats = null)
    {
        if (empty($id)) return array();
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $limit = '';
        if ($page !== null) {
            $limit = 'LIMIT ';
            if ($nb_resultats != null)
                $limit .= (int)$nb_resultats . ' ';
            $limit .= 'OFFSET ' .
                ($page - 1) * ($nb_resultats === null ? 30 : $nb_resultats);
        }

        $groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
        array_unshift($groupes, $_SESSION['groupe']);
        $groupes = implode(',', $groupes);

        $stmt = $dbh->prepare("
	SELECT message_id, message_auteur, utilisateur_id, utilisateur_id_groupe,
	message_texte, message_date, groupe_nom, groupe_logo,
	message_date, sujet_id, sujet_date, sujet_titre, sujet_resolu, sujet_ferme,
	COALESCE(utilisateur_pseudo, 'Anonyme') AS utilisateur_pseudo, utilisateur_avatar

	FROM zcov2_forum_messages
	LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id
	LEFT JOIN zcov2_utilisateurs ON message_auteur = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	LEFT JOIN zcov2_groupes_droits ON gd_id_categorie = sujet_forum_id AND gd_id_groupe IN($groupes)
	LEFT JOIN zcov2_droits ON gd_id_droit = droit_id
	WHERE message_id IN(" . implode(', ', $id) . ") AND droit_nom = 'voir_sujets' AND gd_valeur = 1
	GROUP BY message_id
	$limit");

        $stmt->execute();
        return $stmt->fetchAll();
    }

}