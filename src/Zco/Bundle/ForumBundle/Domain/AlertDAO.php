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
final class AlertDAO
{
    /**
     * Marquer une alerte comme résolue.
     *
     * @param integer $id				L'id de l'alerte.
     * @return void
     */
    public static function AlerteResolue($id, $id_u)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("UPDATE zcov2_forum_alertes " .
            "SET alerte_resolu = 1, alerte_id_admin = :u " .
            "WHERE alerte_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':u', $id_u);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Marquer une alerte comme résolue.
     *
     * @param integer $id				L'id de l'alerte.
     * @return void
     */
    public static function AlerteNonResolue($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('UPDATE zcov2_forum_alertes 
            SET alerte_resolu = 0, alerte_id_admin = null 
            WHERE alerte_id = ?');
        $stmt->execute([$id]);
        $stmt->closeCursor();
    }

    public static function InfosAlerte($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT * from zcov2_forum_alertes WHERE alerte_id = ?');
        $stmt->execute([$id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $res;
    }

    /**
     * Retourne le nombre d'alertes non résolues.
     * @return integer
     */
    public static function CompterAlertes($statut)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("
	SELECT COUNT(*) AS nb
	FROM zcov2_forum_alertes
	WHERE resolu = ?");
        $stmt->execute([$statut]);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Vérifie si on a le droit d'alerter sur un sujet (pas d'alerte en cours).
     * @param integer $id					L'id du sujet.
     * @return boolean
     */
    public static function VerifierAutorisationAlerter($id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("
	SELECT COUNT(alerte_id) AS nb
	FROM zcov2_forum_alertes
	WHERE alerte_sujet_id = :s AND alerte_resolu = 0");
        $stmt->bindParam(':s', $id);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public static function EnregistrerNouvelleAlerte(array $data)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('INSERT INTO zcov2_forum_alertes (auteur, 
          sujet_id, `date`, raison, resolu, ip)
	      VALUES (:id_utilisateur, :id_sujet, NOW(), :texte, 0, :ip)');
        $stmt->bindParam(':id_utilisateur', $data['utilisateur_id']);
        $stmt->bindParam(':id_sujet', $data['sujet_id']);
        $stmt->bindParam(':texte', $data['raison']);
        $stmt->bindValue(':ip', $data['ip']);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Liste toutes les alertes, par sujet et / ou par résolution.
     * @param null|boolean $solved			Alerte résolue ?
     * @param null|integer $sujet_id		ID de sujet.
     * @return array
     */
    public function ListerAlertes($solved = null, $sujet_id = null)
    {
        $query = \Doctrine_Query::create()
            ->select('a.id, a.resolu, a.date, a.raison, a.ip, u1.id, u2.pseudo, '.
                'u2.id, u2.pseudo, s.id, s.titre, s.ferme, s.corbeille, '.
                'g1.class, g2.class, s.forum_id')
            ->from('ForumAlerte a')
            ->leftJoin('a.Utilisateur u1')
            ->leftJoin('a.Admin u2')
            ->leftJoin('a.Sujet s')
            ->leftJoin('u1.Groupe g1')
            ->leftJoin('u2.Groupe g2')
            ->orderBy('a.resolu, a.date DESC');
        if (!is_null($solved))
        {
            $query->addWhere('a.resolu = ?', $solved);
        }
        if (!is_null($sujet_id))
        {
            $query->addWhere('a.sujet_id = ?', $sujet_id);
        }
        $alertes = $query->execute();

        //Tri selon les droits.
        if(is_null($sujet_id))
        {
            foreach($alertes as &$alerte)
            {
                if(!verifier('voir_alertes', $alerte->Sujet['forum_id']))
                    unset($alerte);
            }
        }

        /*$dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT a.id, a.resolu, a.date, a.raison, a.ip, u1.id, u2.pseudo,
            u2.id, u2.pseudo, s.id, s.titre, s.ferme, s.corbeille,
            g1.class, g2.class, s.forum_id
            FROM zcov2_forum_alertes a
            LEFT JOIN zcov2_utilisateurs u1 ON a.utilisateur_id = u1.utilisateur_id
            LEFT JOIN zcov2_utilisateurs u2 ON a.admin_id = u2.utilisateur_id
            LEFT JOIN u');
        $stmt->bindParam(':id_utilisateur', $data['utilisateur_id']);
        $stmt->bindParam(':id_sujet', $data['sujet_id']);
        $stmt->bindParam(':texte', $data['raison']);
        $stmt->bindValue(':ip', $data['ip']);
        $stmt->execute();
        $stmt->closeCursor();*/

        return $alertes;
    }
}