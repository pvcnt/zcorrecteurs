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
     * Envoie un MP automatique (envoyé par le bot zGardien).
     *
     * @param  string $titre Titre du MP
     * @param  string $SousTitre Sous-titre du MP
     * @param  string|array $participants Pseudo du destinataire ou tableau des pseudos des participants
     * @param  string $message Le message formaté en zCode
     * @return integer Identifiant du message créé
     */
    public static function AjouterMPAuto($titre, $SousTitre, $participants, $message)
    {
        if (!is_array($participants)) {
            $participants = array($participants);
        }
        $dbh = \Doctrine_Manager::connection()->getDbh();

        //On crée le nouveau MP
        $stmt = $dbh->prepare("INSERT INTO zcov2_mp_mp (mp_titre, mp_sous_titre,
	mp_date, mp_ferme)
	VALUES (:titre, :sous_titre, NOW(), :ferme)");
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':sous_titre', $SousTitre);
        $stmt->bindValue(':ferme', 1);
        $stmt->execute();

        //On récupère l'id du MP nouvellement créé.
        $NouveauMPID = $dbh->lastInsertId();
        $stmt->closeCursor();

        //On crée le message
        $stmt = $dbh->prepare("INSERT INTO zcov2_mp_messages (mp_message_mp_id,
	mp_message_auteur_id, mp_message_date, mp_message_texte, mp_message_ip)
	VALUES (:NouveauMPID, :auteur, NOW(), :texte, :ip)");
        $stmt->bindParam(':NouveauMPID', $NouveauMPID);
        $stmt->bindValue(':auteur', ID_COMPTE_AUTO);
        $stmt->bindParam(':texte', $message);
        $stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
        $stmt->execute();

        //On récupère l'id de du message nouvellement créé.
        $NouveauMessageID = $dbh->lastInsertId();
        $stmt->closeCursor();

        //Grâce au numéro du message récupéré, on peut updater la table des MP pour indiquer que ce message est le premier et le dernier du MP.
        $stmt = $dbh->prepare("UPDATE zcov2_mp_mp
	SET mp_premier_message_id = :NouveauMessageID, mp_dernier_message_id = :NouveauMessageID
	WHERE mp_id = :NouveauMPID");
        $stmt->bindParam(':NouveauMessageID', $NouveauMessageID);
        $stmt->bindParam(':NouveauMPID', $NouveauMPID);
        $stmt->execute();
        $stmt->closeCursor();

        //Création des participants

        //On va d'abord préparer la requête
        $stmt = $dbh->prepare("INSERT INTO zcov2_mp_participants (mp_participant_mp_id, mp_participant_id, mp_participant_statut, mp_participant_dernier_message_lu)
	VALUES (:mp_id, :participant_id, :statut, :dernier_msg_lu)");
        $stmt->bindParam(':mp_id', $NouveauMPID); //Ce paramètre ne changera pour aucun des participants : on ne le définit qu'une fois.

        //On ajoute déjà le créateur du MP comme participant avec le statut de MP_STATUT_SUPPRIME
        $stmt->bindValue(':participant_id', ID_COMPTE_AUTO);
        $stmt->bindValue(':statut', MP_STATUT_SUPPRIME);
        $stmt->bindParam(':dernier_msg_lu', $NouveauMessageID);
        $stmt->execute();

        //Puis, pour chaque participant, on va l'ajouter en BDD et on vide son cache.
        $stmt->bindValue(':dernier_msg_lu', 0); //Le MP sera non-lu pour tous les autres participants
        foreach ($participants as &$valeur) {
            $stmt->bindParam(':participant_id', $valeur);
            $stmt->bindValue(':statut', MP_STATUT_NORMAL);
            $stmt->execute();

            \Container::cache()->save('MPnonLu' . $valeur, true, 3600);
        }
        $stmt->closeCursor();

        return $NouveauMPID;
    }

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
		LEFT JOIN zcov2_utilisateurs_preferences ON preference_id_utilisateur = utilisateur_id
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
		LEFT JOIN zcov2_utilisateurs_preferences ON preference_id_utilisateur = utilisateur_id
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