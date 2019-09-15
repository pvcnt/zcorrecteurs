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

namespace Zco\Bundle\GroupesBundle\Domain;

final class CredentialsDAO
{
    public static function ListerDroits()
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT droit_id, droit_nom, droit_description, " .
            "droit_choix_categorie, droit_choix_binaire, " .
            "cat_id, cat_nom, cat_niveau " .
            "FROM zcov2_droits " .
            "LEFT JOIN zcov2_categories ON droit_id_categorie = cat_id " .
            "ORDER BY cat_gauche, droit_description");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function InfosDroit($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT droit_id, droit_id_categorie, droit_nom, " .
            "droit_description, droit_choix_categorie, droit_choix_binaire, " .
            "droit_description_longue, " .
            "cat_id, cat_nom, cat_gauche, cat_droite, cat_niveau  " .
            "FROM zcov2_droits " .
            "LEFT JOIN zcov2_categories ON droit_id_categorie = cat_id " .
            "WHERE droit_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function RecupererValeurDroit($droit, $groupe)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT gd_id_categorie, gd_valeur " .
            "FROM zcov2_groupes_droits " .
            "WHERE gd_id_droit = :droit AND gd_id_groupe = :groupe");
        $stmt->bindParam(':droit', $droit);
        $stmt->bindParam(':groupe', $groupe);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function RecupererDroitsGroupe($groupe)
    {
        $cache = \Container::cache();
        if (($retour = $cache->fetch('droits_groupe_' . $groupe)) === false) {
            $dbh = \Doctrine_Manager::connection()->getDbh();
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
            foreach ($rows as $r) {
                if (!$r['droit_choix_categorie'])
                    $retour[$r['droit_nom']] = (int)$r['gd_valeur'];
                else
                    $retour[$r['droit_nom']][$r['gd_id_categorie']] = (int)$r['gd_valeur'];
            }

            $cache->save('droits_groupe_' . $groupe, $retour, 0);
        }
        return $retour;
    }

    public static function EditerDroitGroupe($groupe, $cat, $droit, $valeur)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("INSERT INTO zcov2_groupes_droits(gd_id_groupe, gd_id_droit, gd_id_categorie, gd_valeur) " .
            "VALUES(:groupe, :droit, :cat, :valeur) " .
            "ON DUPLICATE KEY UPDATE gd_valeur = :valeur");
        $stmt->bindParam(':groupe', $groupe);
        $stmt->bindParam(':cat', $cat);
        $stmt->bindParam(':droit', $droit);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
    }

    public static function VerifierDroitsGroupe($groupe)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("SELECT gd_id_categorie, droit_id, droit_nom, " .
            "droit_description, droit_choix_categorie, droit_choix_binaire, " .
            "gd_valeur, COALESCE(c1.cat_id, c2.cat_id) AS cat_id, " .
            "COALESCE(c1.cat_nom, c2.cat_nom) AS cat_nom, " .
            "COALESCE(c1.cat_niveau, c2.cat_niveau) AS cat_niveau " .
            "FROM zcov2_droits " .
            "LEFT JOIN zcov2_groupes_droits ON droit_id = gd_id_droit AND gd_id_groupe = :groupe " .
            "LEFT JOIN zcov2_categories c1 ON gd_id_categorie = c1.cat_id " .
            "LEFT JOIN zcov2_categories c2 ON droit_id_categorie = c2.cat_id " .
            "ORDER BY COALESCE(c1.cat_gauche, c2.cat_gauche)");
        $stmt->bindParam(':groupe', $groupe);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}