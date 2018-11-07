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

namespace Zco\Bundle\BlogBundle\Domain;

/**
 * Modèle gérant tout ce qui est utile pour le blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class BlogDAO
{
    /**
     * Récupère une liste de billets du blog.
     * @param array $params Une liste de paramètres de filtrage.
     * @param int $page Une page (limite la liste des résultats à cette page).
     * @param int $nombre
     * @return array
     */
    public static function ListerBillets($params, $page = null, $nombre = 15)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $limit = '';
        if (!is_null($page)) {
            if ($page > 0) {
                $debut = ($page - 1) * $nombre;
                $fin = $nombre;
            } else {
                //2 billets sur la page d'accueil
                $debut = 0;
                $fin = 2;
            }
            $limit = " LIMIT $debut,$fin";
        }
        $where = $bind = $where_auteurs = array();

        //Création de la clause WHERE
        if (array_key_exists('etat', $params) && (is_numeric($params['etat']) || is_array($params['etat']))) {
            if (is_array($params['etat'])) {
                array_map('intval', $params['etat']);
                $where[] = 'blog_etat IN(' . implode(', ', $params['etat']) . ')';
            } else {
                $where[] = 'blog_etat = :etat';
                $bind['etat'] = $params['etat'];
            }
        }
        if (array_key_exists('id_utilisateur', $params) && is_numeric($params['id_utilisateur'])) {
            $where[] = "blog_id IN(" .
                "SELECT auteur_id_billet " .
                "FROM zcov2_blog_auteurs " .
                "WHERE auteur_id_utilisateur = :id_utilisateur" .
                ((array_key_exists('lecteurs', $params) && $params['lecteurs'] == false) ? " AND auteur_statut > 1" : "") . ")";
            $bind['id_utilisateur'] = $params['id_utilisateur'];
        }
        if (array_key_exists('id_categorie', $params) && is_numeric($params['id_categorie'])) {
            $where[] = "blog_id_categorie = :id_cat";
            $bind['id_cat'] = $params['id_categorie'];
        }
        if (array_key_exists('lecteurs', $params)) {
            if ($params['lecteurs'] == false)
                $where_auteurs[] = 'auteur_statut > 1';
        }
        if (array_key_exists('futur', $params)) {
            if ($params['futur'] == false)
                $where[] = 'blog_date_publication <= NOW()';
        }
        if (array_key_exists('id', $params) && is_array($params['id'])) {
            $where[] = 'blog_id IN(' . implode(', ', $params['id']) . ')';
        }

        //Envoi de la requête
        $stmt = $dbh->prepare("
		SELECT blog_id, blog_lien_topic, blog_commentaires,
			blog_etat, blog_image, blog_date, blog_date_validation,
			blog_date_edition, blog_url_redirection, blog_lien_nom, blog_lien_url,
			version_id, version_titre, version_sous_titre, version_texte, version_intro,
			lunonlu_id_commentaire, cat_id, cat_nom, blog_date_publication, blog_nb_vues
			FROM zcov2_blog
			LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id
			LEFT JOIN zcov2_blog_lunonlu ON lunonlu_id_billet = blog_id AND lunonlu_id_utilisateur = :id_u
			LEFT JOIN zcov2_categories ON cat_id = blog_id_categorie
			WHERE " . ($where ? implode(' AND ', $where) : '1=1') . "
			ORDER BY blog_date_publication DESC, blog_date DESC $limit");
        $stmt->bindParam(':id_u', $_SESSION['id']);
        foreach ($bind as $cle => &$valeur)
            $stmt->bindParam(':' . $cle, $valeur);
        $stmt->execute();
        $liste = $stmt->fetchAll();
        $stmt->closeCursor();

        // Calcul des clés blog_nb_commentaires et dernier_commentaire séparément
        $liste_id = array();
        foreach ($liste as $cle => $valeur) {
            if (!in_array($valeur['blog_id'], $liste_id)) {
                $liste_id[] = $valeur['blog_id'];
            }
        }
        if (empty($liste_id)) {
            return array(array(), array());
        }
        $stmt = $dbh->prepare("SELECT blog_id,
				(SELECT COUNT(*)
				FROM zcov2_blog_commentaires
				WHERE commentaire_id_billet = blog_id)
			AS blog_nb_commentaires,
				(SELECT MAX(commentaire_id)
				FROM zcov2_blog_commentaires
				WHERE commentaire_id_billet = blog_id)
			AS dernier_commentaire
			FROM zcov2_blog
			WHERE blog_id IN (" . implode(',', $liste_id) . ")");

        $stmt->execute();
        $fetchAll = $stmt->fetchAll();
        $stmt->closeCursor();
        $agregate = array();

        foreach ($fetchAll as $f) {
            $agregate[$f['blog_id']] = $f;
        }

        foreach ($liste as &$billet) {
            $billet['blog_nb_commentaires'] = $agregate[$billet['blog_id']]['blog_nb_commentaires'];
            $billet['dernier_commentaire'] = $agregate[$billet['blog_id']]['dernier_commentaire'];
            if (empty($billet['dernier_commentaire']) || $billet['dernier_commentaire'] == $billet['lunonlu_id_commentaire'])
                $billet['lunonlu_id_commentaire'] = null;
        }

        // Récupération des auteurs des billets
        $auteurs = array();
        $where_auteurs[] = "blog_id IN (" . implode(',', $liste_id) . ")";

        $stmt = $dbh->prepare("SELECT blog_id, auteur_statut,
			utilisateur_id, utilisateur_pseudo, groupe_nom 
			FROM zcov2_blog_auteurs
			LEFT JOIN zcov2_utilisateurs ON auteur_id_utilisateur = utilisateur_id
			LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
			LEFT JOIN zcov2_blog ON auteur_id_billet = blog_id
			WHERE " . implode(' AND ', $where_auteurs) . "
			ORDER BY auteur_statut DESC");
        $stmt->execute();
        $fetchAll = $stmt->fetchAll();
        $stmt->closeCursor();

        foreach ($fetchAll as $a) {
            if (!isset($auteurs[$a['blog_id']])) {
                $auteurs[$a['blog_id']] = array();
            }
            $auteurs[$a['blog_id']][] = array(
                'utilisateur_id' => $a['utilisateur_id'],
                'utilisateur_pseudo' => $a['utilisateur_pseudo'],
                'auteur_statut' => $a['auteur_statut'],
            );
        }
        return array($liste, $auteurs);
    }

    /**
     * Compte tous les billets en ligne.
     * @param integer $id ID de la catégorie (null pour toutes les catégories confondues)
     * @return int
     */
    public static function CompterListerBilletsEnLigne($id = null)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $add = (is_null($id)) ? '' : ' AND blog_id_categorie = ' . intval($id);

        $stmt = $dbh->prepare("SELECT COUNT(*) AS nb " .
            "FROM zcov2_blog " .
            "WHERE blog_date_publication <= NOW() AND blog_etat = " . BLOG_VALIDE . $add);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Récupère la liste des billets partageant un tag avec un billet fixé.
     * @param integer $id L'id du billet de référence.
     * @return array
     */
    public static function ListerBilletsLies($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT DISTINCT blog_id, version_titre, version_sous_titre " .
            "FROM zcov2_blog_tags " .
            "LEFT JOIN zcov2_blog ON id_blog = blog_id " .
            "LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
            "WHERE id_tag IN (" .
            "SELECT id_tag " .
            "FROM zcov2_blog_tags " .
            "WHERE id_blog = :id) AND blog_id <> :id " .
            "LIMIT 5");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les infos sur un billet.
     * @param integer $id L'id du billet.
     * @return array
     */
    public static function InfosBillet($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT blog_id, blog_id_categorie, blog_lien_topic, " .
            "blog_commentaires, blog_etat, blog_id_version_courante, blog_nb_vues, " .
            "blog_image, blog_date_validation, blog_date, blog_date_validation, " .
            "blog_date_edition, blog_date_proposition, blog_date_publication, " .
            "(SELECT COUNT(*) FROM zcov2_blog_commentaires WHERE commentaire_id_billet = blog_id) AS blog_nb_commentaires, " .
            "auteur_date, auteur_statut, utilisateur_id, utilisateur_pseudo, " .
            "utilisateur_email, blog_url_redirection, blog_lien_nom, blog_lien_url, " .
            "version_id, version_titre, version_sous_titre, version_texte, " .
            "version_ip, version_intro, cat_id, cat_nom, groupe_nom " .
            "FROM zcov2_blog_auteurs " .
            "LEFT JOIN zcov2_utilisateurs Ma ON auteur_id_utilisateur = utilisateur_id " .
            "LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id " .
            "LEFT JOIN zcov2_blog ON auteur_id_billet = blog_id " .
            "LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
            "LEFT JOIN zcov2_categories ON cat_id = blog_id_categorie " .
            "WHERE auteur_id_billet = :id " .
            "ORDER BY auteur_statut DESC");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un billet.
     * @return integer                L'id du billet.
     */
    public static function AjouterBillet()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //On ajoute une nouvelle version
        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_versions(version_id_utilisateur, " .
            "version_date, version_ip, version_titre, version_sous_titre, " .
            "version_texte, version_intro, version_id_billet, version_commentaire) " .
            "VALUES(:id_utilisateur, NOW(), :ip, :titre, :sous_titre, :texte, " .
            ":intro, NULL, 'Création du billet.')");
        $stmt->bindParam(':id_utilisateur', $_SESSION['id']);
        $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
        $stmt->bindParam(':titre', $_POST['titre']);
        $stmt->bindParam(':sous_titre', $_POST['sous_titre']);
        $stmt->bindParam(':texte', $_POST['texte']);
        $stmt->bindParam(':intro', $_POST['intro']);
        $stmt->execute();
        $id_version = $dbh->lastInsertId();

        //On ajoute le billet
        $stmt = $dbh->prepare("INSERT INTO zcov2_blog(blog_id_categorie, blog_date, " .
            "blog_date_edition, blog_etat, blog_commentaires, " .
            "blog_id_version_courante, blog_image) " .
            "VALUES(:id_categorie, NOW(), NOW(), :etat, :comms, :id_version, :image)");
        $stmt->bindParam(':id_categorie', $_POST['categorie']);
        $stmt->bindValue(':image', 'uploads/miniatures/blog/defaut.png');
        $stmt->bindValue(':etat', BLOG_BROUILLON);
        $stmt->bindValue(':comms', COMMENTAIRES_OK);
        $stmt->bindParam(':id_version', $id_version);
        $stmt->execute();
        $id_blog = $dbh->lastInsertId();

        //On met à jour la version
        $stmt = $dbh->prepare("UPDATE zcov2_blog_versions " .
            "SET version_id_billet = :id_billet " .
            "WHERE version_id = :id_version");
        $stmt->bindParam(':id_billet', $id_blog);
        $stmt->bindParam(':id_version', $id_version);
        $stmt->execute();

        //Et on ajoute le créateur
        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_auteurs(auteur_id_utilisateur, " .
            "auteur_id_billet, auteur_statut, auteur_date) " .
            "VALUES(:id_utilisateur, :id_billet, 3, NOW())");
        $stmt->bindParam(':id_utilisateur', $_SESSION['id']);
        $stmt->bindParam(':id_billet', $id_blog);
        $stmt->execute();

        return $id_blog;
    }

    /**
     * Redimensionne l'image pour en faire une icône pour un billet.
     * @param integer $id L'id du billet.
     * @param string $url L'url de l'image.
     * @return array                array('succes' => boolean, 'path' => string)
     */
    public static function AjouterBilletImage($id, $url)
    {
        //On corrige l'url si elle est mal formée
        $url = trim($url, '/');
        $url = preg_replace('`^http://www.zcorrecteurs.fr/uploads/(.+)$`', BASEPATH . '/uploads/$1', $url);

        try {
            $thumbnail = \Container::imagine()
                ->open($url)
                ->thumbnail(new \Imagine\Image\Box(100, 100));
            $path = BASEPATH . '/web/uploads/miniatures/blog/' . $id . '.png';
            $thumbnail->save($path);

            return array(true, 'uploads/miniatures/blog/' . $id . '.png');
        } catch (\InvalidArgumentException $e) {
            return array(false, 0);
        } catch (\Exception $e) {
            return array(false, 2);
        }
    }

    /**
     * Modifie un billet.
     * @param integer $id L'id du billet.
     * @param array $params La liste des champs à modifier.
     * @return void
     */
    public static function EditerBillet($id, $params)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $set_billet = array();
        $set_version = false;
        $bind_billet = array();
        $infos = null;
        $champs_versionnes = array('titre', 'sous_titre', 'texte', 'intro', 'commentaire');

        foreach ($params as $cle => $valeur) {
            //Si on modifie un champ versionné.
            if (in_array($cle, $champs_versionnes)) {
                $set_version = true;
            } //Sinon on modifie la table des billets (et non pas un champ versionné).
            else {
                if (is_null($valeur))
                    $set_billet[] = 'blog_' . $cle . ' = NULL';
                elseif (is_int($valeur))
                    $set_billet[] = 'blog_' . $cle . ' = ' . (int)$valeur;
                elseif ($valeur == 'NOW()')
                    $set_billet[] = 'blog_' . $cle . ' = NOW()';
                else {
                    $set_billet[] = 'blog_' . $cle . ' = :' . $cle;
                    $bind_billet[$cle] = $valeur;
                }
            }
        }

        //Modification de la table des version si nécessaire.
        if ($set_version == true) {
            $commentaire = !empty($params['commentaire']) ? $params['commentaire'] : '';

            $stmt = $dbh->prepare("SELECT MAX(version_id_fictif) + 1 " .
                "FROM zcov2_blog_versions " .
                "WHERE version_id_billet = :id_b");
            $stmt->bindParam(':id_b', $id);
            $stmt->execute();
            $id_v = $stmt->fetchColumn();
            $stmt->closeCursor();

            $stmt = $dbh->prepare("INSERT INTO zcov2_blog_versions(version_id_billet, " .
                "version_id_utilisateur, version_id_fictif, version_date, " .
                "version_ip, version_titre, version_sous_titre, version_texte, " .
                "version_intro, version_commentaire) " .
                "VALUES(:id_b, :id_u, :id_v, NOW(), :ip, :titre, :sous_titre, " .
                ":texte, :intro, :commentaire)");
            $stmt->bindParam(':id_b', $id);
            $stmt->bindParam(':id_v', $id_v);
            $stmt->bindParam(':id_u', $_SESSION['id']);
            $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
            $stmt->bindParam(':titre', $params['titre']);
            $stmt->bindParam(':sous_titre', $params['sous_titre']);
            $stmt->bindParam(':texte', $params['texte']);
            $stmt->bindParam(':intro', $params['intro']);
            $stmt->bindParam(':commentaire', $commentaire);
            $stmt->execute();
            $stmt->closeCursor();
            $set_billet[] = 'blog_id_version_courante = ' . $dbh->lastInsertId();
        }

        //Modification de la table des billets.
        $set_billet[] = 'blog_date_edition = CURRENT_TIMESTAMP';
        $stmt = $dbh->prepare("UPDATE zcov2_blog " .
            "SET " . implode(', ', $set_billet) . " " .
            "WHERE blog_id = :id");
        $stmt->bindParam(':id', $id);
        foreach ($bind_billet as $cle => &$valeur)
            $stmt->bindParam(':' . $cle, $valeur);
        $stmt->execute();
    }

    /**
     * Ajoute un auteur à  un billet.
     * @param integer $id_billet L'id du billet concerné.
     * @param integer $id_utilisateur L'id de l'utilisateur.
     * @return void
     */
    public static function AjouterAuteur($id_billet, $id_utilisateur, $statut)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $statut = in_array($statut, array(1, 2, 3)) ? $statut : 1;

        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_auteurs(auteur_id_utilisateur, " .
            "auteur_id_billet, auteur_statut, auteur_date) " .
            "VALUES(:id_utilisateur, :id_billet, :statut, NOW())");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_billet', $id_billet);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
    }

    /**
     * Édite un auteur.
     * @param integer $id_utilisateur L'id de l'ancien auteur.
     * @param integer $id_billet L'id du billet concerné.
     * @param integer $new_id L'id du nouvel auteur.
     * @param integer $statut Le statut de l'auteur.
     * @return void
     */
    public static function EditerAuteur($id_utilisateur, $id_billet, $new_id, $statut)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $statut = in_array($statut, array(1, 2, 3)) ? $statut : 1;

        $stmt = $dbh->prepare("
	UPDATE zcov2_blog_auteurs
	SET auteur_id_utilisateur = :new_id, auteur_statut = :new_statut
	WHERE auteur_id_utilisateur = :id_utilisateur AND auteur_id_billet = :id_billet");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_billet', $id_billet);
        $stmt->bindParam(':new_id', $new_id);
        $stmt->bindParam(':new_statut', $statut);
        $stmt->execute();
    }

    /**
     * Supprime un auteur.
     * @param integer $id_utilisateur L'id de l'auteur à supprimer.
     * @param integer $id_billet L'id du billet concerné.
     * @return void
     */
    public static function SupprimerAuteur($id_utilisateur, $id_billet)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	DELETE FROM zcov2_blog_auteurs
	WHERE auteur_id_utilisateur = :id_utilisateur AND auteur_id_billet = :id_billet");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_billet', $id_billet);
        $stmt->execute();
    }

    /**
     * Supprime un billet.
     * @param integer $id L'id du billet à supprimer.
     */
    public static function SupprimerBillet($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("DELETE FROM zcov2_blog WHERE blog_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    /**
     * Compte le nombre de billets proposés.
     * @return integer
     */
    public static function NombreBilletsProposes()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT COUNT(*) AS nombre
		FROM zcov2_blog
		WHERE blog_etat = " . BLOG_PROPOSE);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Valide un billet.
     * @param integer $id L'id du billet à valider.
     * @param boolean $conserver_date_pub Doit-on laisser la date de publication indiquée ?
     * @return void
     */
    public static function ValiderBillet($id, $conserver_date_pub = false)
    {
        //Mise à jour du billet
        if (!$conserver_date_pub)
            self::EditerBillet($id, array('etat' => BLOG_VALIDE, 'date_publication' => 'NOW()'));
        else
            self::EditerBillet($id, array('etat' => BLOG_VALIDE));

        //Suppression des commentaires
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("DELETE FROM zcov2_blog_commentaires " .
            "WHERE commentaire_id_billet = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt = $dbh->prepare("DELETE FROM zcov2_blog_lunonlu " .
            "WHERE lunonlu_id_billet = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    /**
     * Pour le sitemap, liste les billets avec leur titre et leur id.
     * @return array
     */
    public static function ListerBilletsId()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT version_titre, blog_id " .
            "FROM zcov2_blog " .
            "LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
            "WHERE blog_etat = " . BLOG_VALIDE . " " .
            "ORDER BY blog_date_publication DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajout d'une visite sur le flux.
     * @param integer $ip L'id du visiteur.
     * @param integer $id_cat L'id de la catégorie.
     * @param string $navigateur L'user-agent du visiteur.
     */
    public static function AjouterVisiteFlux($ip, $id_cat, $navigateur)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_flux_visites(visite_date, visite_ip, " .
            "visite_id_categorie, visite_user_agent, visite_nb_vues) " .
            "VALUES(NOW(), :ip, :id_cat, :navigateur, 1) " .
            "ON DUPLICATE KEY UPDATE visite_nb_vues = visite_nb_vues + 1");
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':id_cat', $id_cat);
        $stmt->bindParam(':navigateur', $navigateur);
        $stmt->execute();
    }

    /**
     * Ajoute un tag à un billet.
     * @param integer $id_b L'id du billet.
     * @param integer $id_t L'id du tag.
     * @return void
     */
    public static function AjouterTagBillet($id_b, $id_t)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_tags(id_tag, id_blog) " .
            "VALUES(:id_t, :id_b)");
        $stmt->bindParam(':id_b', $id_b);
        $stmt->bindParam(':id_t', $id_t);
        $stmt->execute();
    }

    /**
     * Supprime un tag d'un billet.
     * @param integer $id_b L'id du billet.
     * @param integer $id_t L'id du tag.
     * @return void
     */
    public static function SupprimerTagBillet($id_b, $id_t)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("DELETE FROM zcov2_blog_tags " .
            "WHERE id_tag = :id_t AND id_blog = :id_b");
        $stmt->bindParam(':id_b', $id_b);
        $stmt->bindParam(':id_t', $id_t);
        $stmt->execute();
    }

    /**
     * Récupère la liste des tags associés à un billet.
     * @param integer $id L'id du billet.
     * @return array
     */
    public static function ListerTagsBillet($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT id, nom, description " .
            "FROM zcov2_blog_tags " .
            "LEFT JOIN zcov2_tags ON id_tag = id " .
            "WHERE id_blog = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function ChercherBillets($titre)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //Envoi de la requête
        $stmt = $dbh->prepare("
		SELECT blog_id, version_titre, version_texte, cat_id, cat_nom
			FROM zcov2_blog
			LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id
			LEFT JOIN zcov2_categories ON cat_id = blog_id_categorie
			WHERE version_titre LIKE :titre
			ORDER BY version_titre ASC");
        $stmt->bindValue(':titre', '%' . $titre . '%');
        $stmt->execute();
        $liste = $stmt->fetchAll();
        $stmt->closeCursor();
        return $liste;
    }

    public static function BilletAleatoire($categories = array())
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //Envoi de la requête
        $stmt = $dbh->prepare('SELECT blog_id
		FROM zcov2_blog ' .
            'WHERE blog_etat = ' . BLOG_VALIDE . ' AND blog_date_publication <= NOW() ' . ($categories == array() ? '' : 'AND blog_id_categorie IN(' . implode(',', $categories) . ') ') .
            'ORDER BY RAND() LIMIT 0, 1');
        $stmt->execute();
        $liste = $stmt->fetch();
        $stmt->closeCursor();
        return $liste['blog_id'];
    }

    public static function BlogIncrementerVues($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("UPDATE zcov2_blog SET blog_nb_vues = blog_nb_vues+1 WHERE blog_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->closeCursor();
    }


    /**
     * Infos de validation sur une version.
     * @param integer $id L'id de la version.
     * @return array
     */
    public static function InfosValidationVersion($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT valid_commentaire, utilisateur_id, utilisateur_pseudo
	FROM zcov2_blog_validation
	LEFT JOIN zcov2_utilisateurs ON valid_id_utilisateur = utilisateur_id
	WHERE valid_id_version = :id
	ORDER BY valid_date DESC
	LIMIT 0, 1");
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $return = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $return;
    }

    /**
     * Ajoute une entrée dans l'historique de validation.
     * @param integer $id_billet L'id du billet à proposer.
     * @param integer $id_version L'id de la version proposée
     * @return void
     */
    public static function AjouterHistoriqueValidation($id_billet, $id_u, $id_version, $texte, $decision)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("INSERT INTO zcov2_blog_validation(valid_id_billet, " .
            "valid_id_utilisateur, valid_id_version, valid_date, valid_ip, " .
            "valid_commentaire, valid_decision) " .
            "VALUES(:id_billet, :id_utilisateur, :id_version, NOW(), :ip, " .
            ":commentaire, :decision)");
        $stmt->bindParam(':id_billet', $id_billet);
        $stmt->bindParam(':id_utilisateur', $id_u);
        $stmt->bindParam(':id_version', $id_version);
        $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
        $stmt->bindParam(':commentaire', $texte);
        $stmt->bindParam(':decision', $decision);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Récupère l'historique de validation d'un billet.
     * @param integer $id L'id du billet.
     * @return array
     */
    public static function HistoriqueValidation($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT valid_id, valid_ip, valid_id_version, " .
            "valid_decision, valid_commentaire, valid_date, utilisateur_id, " .
            "utilisateur_pseudo " .
            "FROM zcov2_blog_validation " .
            "LEFT JOIN zcov2_utilisateurs ON valid_id_utilisateur = utilisateur_id " .
            "WHERE valid_id_billet = :id " .
            "ORDER BY valid_date");
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Liste les versions d'un billet.
     *
     * @param integer $id L'id du billet.
     * @return array
     */
    public static function ListerVersions($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //On récupère les versions
        $stmt = $dbh->prepare("SELECT version_titre, version_sous_titre,
			version_texte, version_intro, version_id, version_ip,
			utilisateur_id, utilisateur_pseudo, version_date, version_id_fictif,
			version_commentaire
			FROM zcov2_blog_versions
			LEFT JOIN zcov2_utilisateurs ON version_id_utilisateur = utilisateur_id
			WHERE version_id_billet = :id
			ORDER BY version_date");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $return = $stmt->fetchAll();
        $stmt->closeCursor();

        //Et on compare
        $texte_old = '';
        $intro_old = '';
        $titre_old = '';
        $sous_titre_old = '';
        $id_old = 0;
        $i = 0;
        while ($i < count($return)) {
            if ($return[$i]['version_texte'] != $texte_old) $return[$i]['texte'] = 'rouge';
            else $return[$i]['texte'] = 'vertf';
            if ($return[$i]['version_intro'] != $intro_old) $return[$i]['intro'] = 'rouge';
            else $return[$i]['intro'] = 'vertf';
            if ($return[$i]['version_titre'] != $titre_old) $return[$i]['titre'] = 'rouge';
            else $return[$i]['titre'] = 'vertf';
            if ($return[$i]['version_sous_titre'] != $sous_titre_old) $return[$i]['sous_titre'] = 'rouge';
            else $return[$i]['sous_titre'] = 'vertf';

            $return[$i]['id_precedent'] = $id_old;
            $id_old = $return[$i]['version_id'];
            $texte_old = $return[$i]['version_texte'];
            $titre_old = $return[$i]['version_titre'];
            $sous_titre_old = $return[$i]['version_sous_titre'];
            $intro_old = $return[$i]['version_intro'];
            $i++;
        }

        return array_reverse($return);
    }

    /**
     * Infos sur une version.
     *
     * @param integer $id L'id de la version.
     * @return array
     */
    public static function InfosVersion($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT version_titre, version_sous_titre, version_intro, " .
            "version_texte, version_id, version_ip, version_id_billet, version_commentaire, " .
            "utilisateur_id, utilisateur_pseudo, version_date, version_id_billet " .
            "FROM zcov2_blog_versions " .
            "LEFT JOIN zcov2_utilisateurs ON utilisateur_id = version_id_utilisateur " .
            "WHERE version_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}