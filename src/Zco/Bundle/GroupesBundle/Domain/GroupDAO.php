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

namespace Zco\Bundle\GroupesBundle\Domain;

final class GroupDAO
{
    public static function ListerGroupes()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT groupe_id, groupe_code, groupe_nom, groupe_logo, groupe_logo_feminin, groupe_class, groupe_sanction, groupe_team, groupe_secondaire, (SELECT COUNT(*) FROM zcov2_utilisateurs WHERE utilisateur_id_groupe = groupe_id) AS groupe_effectifs
	FROM zcov2_groupes
	WHERE groupe_secondaire = 0
	ORDER BY groupe_nom");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function ListerGroupesSecondaires()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT groupe_id, groupe_nom, groupe_logo, groupe_logo_feminin, groupe_class, groupe_sanction, groupe_team, groupe_secondaire, (SELECT COUNT(*) FROM zcov2_groupes_secondaires WHERE zcov2_groupes_secondaires.groupe_id = zcov2_groupes.groupe_id) AS groupe_effectifs
	FROM zcov2_groupes
	WHERE groupe_secondaire = 1
	ORDER BY groupe_nom");

        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * Liste les groupes secondaires d'un membre
     * @param integer $id           L'id du membre
     * @return array
     */
    public static function ListerGroupesSecondairesUtilisateur($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare('SELECT gs.groupe_id, g.groupe_nom, g.groupe_class '
            .'FROM zcov2_groupes_secondaires gs '
            .'LEFT JOIN zcov2_groupes g USING(groupe_id) '
            .'LEFT JOIN zcov2_utilisateurs u USING(utilisateur_id) '
            .'WHERE utilisateur_id = :id');
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $retour = $stmt->fetchAll();
        $stmt->closeCursor();
        return $retour;
    }

    public static function ModifierGroupesSecondairesUtilisateur($utilisateur_id, $groupes_id)
    {
        $groupes_precedents = self::ListerGroupesSecondairesUtilisateur($utilisateur_id);
        $groupes_precedents_id = array();

        // Supression des groupes en trop
        foreach ($groupes_precedents as $groupe)
        {
            $groupes_precedents_id[] = $groupe['groupe_id'];
            if (!in_array($groupe['groupe_id'], $groupes_id))
            {
                self::SupprimerGroupeSecondaireUtilisateur(
                    $utilisateur_id, $groupe['groupe_id']);
            }
        }

        // Ajout des groupes manquants
        foreach($groupes_id as $groupe_id)
        {
            if (!in_array($groupe_id, $groupes_precedents_id))
            {
                self::AjouterGroupeSecondaireUtilisateur($utilisateur_id, $groupe_id);
            }
        }
    }

    /**
     * Ajoute un groupe secondaire à un membre.
     * @param integer $utilisateur_id   L'id du membre
     * @param integer $groupe_id        L'id du groupe
     */
    public static function AjouterGroupeSecondaireUtilisateur($utilisateur_id, $groupe_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare('INSERT INTO zcov2_groupes_secondaires (utilisateur_id, groupe_id) VALUES (?, ?)');
        $stmt->execute(array($utilisateur_id, $groupe_id));

        self::AjouterGroupeHistoriqueUtilisateur($utilisateur_id, $groupe_id, null);
    }

    /**
     * Supprime un groupe secondaire à un membre.
     * @param integer $utilisateur_id   L'id du membre
     * @param integer $groupe_id        L'id du groupe
     */
    public static function SupprimerGroupeSecondaireUtilisateur($utilisateur_id, $groupe_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare('DELETE FROM zcov2_groupes_secondaires '
            .'WHERE utilisateur_id = ? '
            .'AND groupe_id = ?');

        $stmt->execute(array($utilisateur_id, $groupe_id));

        self::AjouterGroupeHistoriqueUtilisateur($utilisateur_id, null, $groupe_id);
    }

    public static function InfosGroupe($id)
    {
        $sql = "SELECT groupe_id, groupe_code, groupe_nom, groupe_logo, groupe_logo_feminin, groupe_class, groupe_sanction, groupe_team, groupe_secondaire, groupe_description, (SELECT COUNT(*) FROM zcov2_utilisateurs WHERE utilisateur_id_groupe = groupe_id) AS groupe_effectifs
	FROM zcov2_groupes";
        if (is_numeric($id)) {
            $sql .= ' WHERE groupe_id = ?';
        } else {
            $sql .= ' WHERE groupe_code = ?';
        }

        return \Doctrine_Manager::connection()->fetchRow($sql, [$id]);
    }

    public static function AjouterGroupe(array $data)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $sanction = isset($data['sanction']) ? 1 : 0;
        $team = isset($data['team']) ? 1 : 0;
        $secondaire = isset($data['secondaire']) ? 1 : 0;

        $stmt = $dbh->prepare("
	INSERT INTO zcov2_groupes(groupe_nom, groupe_logo, groupe_logo_feminin, groupe_class, groupe_sanction, groupe_team, groupe_secondaire)
	VALUES(:nom, :logo, :logof, :class, :sanction, :team, :secondaire)");
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':logo', $data['logo']);
        $stmt->bindParam(':logof', $data['logo_feminin']);
        $stmt->bindParam(':class', $data['class']);
        $stmt->bindParam(':sanction', $sanction);
        $stmt->bindParam(':team', $team);
        $stmt->bindParam(':secondaire', $secondaire);
        $stmt->execute();
        $id = $dbh->lastInsertId();
        $stmt->closeCursor();

        //Si on doit copier les droits d'un autre groupe.
        if(!empty($data['groupe']) && is_numeric($data['groupe']))
        {
            $stmt = $dbh->prepare("SELECT gd_id_categorie, gd_id_droit, gd_valeur
			FROM zcov2_groupes_droits
			WHERE gd_id_groupe = :id");
            $stmt->bindParam(':id', $data['groupe']);
            $stmt->execute();
            $donnees = $stmt->fetchAll();
            $stmt->closeCursor();
            foreach($donnees as $gd)
            {
                $stmt = $dbh->prepare("INSERT INTO zcov2_groupes_droits(gd_id_groupe,
				gd_id_droit, gd_id_categorie, gd_valeur)
				VALUES(:id_grp, :id_drt, :id_cat, :valeur)");
                $stmt->bindParam(':id_grp', $id);
                $stmt->bindParam(':id_cat', $gd['gd_id_categorie']);
                $stmt->bindParam(':id_drt', $gd['gd_id_droit']);
                $stmt->bindParam(':valeur', $gd['gd_valeur']);
                $stmt->execute();
                $stmt->closeCursor();
            }
        }

        return $id;
    }

    public static function EditerGroupe($id, array $data)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $sanction = isset($data['sanction']) ? 1 : 0;
        $team = isset($data['team']) ? 1 : 0;
        $secondaire = isset($data['secondaire']) ? 1 : 0;

        $stmt = $dbh->prepare("UPDATE zcov2_groupes
	SET groupe_nom = :nom, groupe_description = :description, groupe_logo = :logo, groupe_logo_feminin = :logof,
	groupe_class = :class, groupe_sanction = :sanction, groupe_team = :team, groupe_secondaire = :secondaire
	WHERE groupe_id = :id");
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':logo', $data['logo']);
        $stmt->bindParam(':logof', $data['logo_feminin']);
        $stmt->bindParam(':class', $data['class']);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':sanction', $sanction);
        $stmt->bindParam(':team', $team);
        $stmt->bindParam(':secondaire', $secondaire);

        $stmt->execute();

    }

    public static function SupprimerGroupe($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $defaultGroup = self::InfosGroupe(\Groupe::DEFAULT);

        $stmt = $dbh->prepare("DELETE FROM zcov2_groupes WHERE groupe_id=:id");
        $stmt->bindParam(':id', $id);

        $stmt2 = $dbh->prepare("UPDATE zcov2_utilisateurs SET utilisateur_id_groupe=:groupe WHERE utilisateur_id_groupe=:id");
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':groupe', $defaultGroup['groupe_id']);

        $stmt->execute();
        $stmt2->execute();

        return true;
    }

    public static function ChangerGroupeUtilisateur($userId, $newGroupId)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // On vérifie le groupe actuel du membre
        $stmt = $dbh->prepare('SELECT utilisateur_id_groupe '
            .'FROM zcov2_utilisateurs '
            .'WHERE utilisateur_id = :id');
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $groupe = $stmt->fetchColumn();

        // Si on change bien de groupe, on effectue l'action
        if($groupe !== false && $newGroupId != $groupe)
        {
            $stmt = $dbh->prepare('UPDATE zcov2_utilisateurs '
                .'SET utilisateur_id_groupe = :groupe '
                .'WHERE utilisateur_id = :id');
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':groupe', $newGroupId);
            $stmt->execute();

            self::AjouterGroupeHistorique($userId, $newGroupId);
        }
    }

    /**
     *	Fonction qui ajoute un changement de groupe dans l'historique
     *	@param integer 	$id			Id du membre qui subit le changement
     *	@param integer	$groupe	Id de son nouveau groupe
     *	@return void
     **/
    public static function AjouterGroupeHistorique($id, $groupe)
    {
        include_once(__DIR__.'/../../UserBundle/modeles/utilisateurs.php');
        $InfoMembre = InfosUtilisateur($id);
        self::AjouterGroupeHistoriqueUtilisateur($id, $groupe, $InfoMembre['utilisateur_id_groupe']);
    }

    public static function AjouterGroupeHistoriqueUtilisateur($id, $nouveau_groupe, $ancien_groupe)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	INSERT INTO zcov2_historique_groupes (chg_utilisateur_id, chg_responsable, chg_date, chg_nouveau_groupe, chg_ancien_groupe)
	VALUES(:id, :responsable, NOW(), :groupe, :ancien)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':responsable', $_SESSION['id']);
        $stmt->bindParam(':groupe', $nouveau_groupe);
        $stmt->bindParam(':ancien', $ancien_groupe);
        $stmt->execute();
    }

    /**
     *	Fonction qui liste les changements de groupes d'un membre
     *	@param integer $id		Id du membre
     *	@return array
     **/
    public static function ListerChangementGroupeMembre($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("
	SELECT chg_id, chg_date, utilisateur_id, IFNULL(utilisateur_pseudo, 'Anonyme') as pseudo_responsable,
	Ga.groupe_nom as ancien_groupe, Gb.groupe_nom as nouveau_groupe, Ga.groupe_class as couleur_ancien_groupe, 
	Gb.groupe_class as couleur_nouveau_groupe, Gb.groupe_secondaire as nouveau_groupe_secondaire,
	Ga.groupe_secondaire as ancien_groupe_secondaire
		FROM zcov2_historique_groupes
		LEFT JOIN zcov2_groupes Ga ON Ga.groupe_id = chg_ancien_groupe
		LEFT JOIN zcov2_groupes Gb ON Gb.groupe_id = chg_nouveau_groupe
		LEFT JOIN zcov2_utilisateurs ON utilisateur_id = chg_responsable
	WHERE chg_utilisateur_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}