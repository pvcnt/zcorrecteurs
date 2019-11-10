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

namespace Zco\Bundle\UserBundle\Domain;

final class UserDAO
{
    /**
     * Récupère les informations sur un membre à partir de son pseudo ou de son id.
     *
     * @param  string|integer $search Identifiant du membre ou pseudo
     * @return array
     */
    public static function InfosUtilisateur($search)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        if (is_numeric($search)) {
            $stmt = $dbh->prepare("SELECT *, CASE
		WHEN utilisateur_date_naissance IS NULL THEN 0
		ELSE FLOOR(DATEDIFF(NOW(), utilisateur_date_naissance) / 365)
		END AS age
		FROM zcov2_utilisateurs
		LEFT JOIN zcov2_groupes ON utilisateur_id_groupe=groupe_id
		WHERE utilisateur_id = :id");
            $stmt->bindParam(':id', $search);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } else {
            $stmt = $dbh->prepare("SELECT *, CASE
		WHEN utilisateur_date_naissance IS NULL THEN 0
		ELSE DATEDIFF(NOW(), utilisateur_date_naissance) / 365
		END AS age
		FROM zcov2_utilisateurs
		LEFT JOIN zcov2_groupes ON utilisateur_id_groupe=groupe_id
		WHERE utilisateur_pseudo = :pseudo");
            $stmt->bindParam(':pseudo', $search);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }

    public static function getPreferences($userId)
    {
        $conn = \Doctrine_Manager::connection();
        $row = $conn->fetchRow('select preference_decalage
            from zcov2_utilisateurs_preferences 
            where preference_id_utilisateur = ?',
            [$userId]
        );

        return [
            'time_difference' => $row['preference_decalage'],
        ];
    }

    public static function savePreferences($userId, array $data)
    {
        $conn = \Doctrine_Manager::connection();
        $count = $conn->exec('update zcov2_utilisateurs_preferences
            set preference_activer_email_mp = ?, preference_decalage = ?
            where preference_id_utilisateur = ?',
            [$data['time_difference'], $userId]
        );
        if (0 === $count) {
            $conn->exec('insert into zcov2_utilisateurs_preferences
                (preference_id_utilisateur, preference_activer_email_mp, preference_decalage)
                values(?, ?, ?)',
                [$userId, $data['time_difference']]
            );
        }
    }
}