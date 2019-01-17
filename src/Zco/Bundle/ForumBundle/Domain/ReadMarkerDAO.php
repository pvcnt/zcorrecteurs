<?php

namespace Zco\Bundle\ForumBundle\Domain;

/**
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
final class ReadMarkerDAO
{
    public static function MarquerForumsLus($lu)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($lu) {
            $stmt = $dbh->prepare('UPDATE zcov2_utilisateurs SET '
                . 'utilisateur_derniere_lecture = CURRENT_TIMESTAMP '
                . 'WHERE utilisateur_id = :id');
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();
        } else {
            $stmt = $dbh->prepare('UPDATE zcov2_utilisateurs SET '
                . 'utilisateur_derniere_lecture = 0 '
                . 'WHERE utilisateur_id = :id');
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();

            $stmt = $dbh->prepare('UPDATE zcov2_forum_lunonlu SET '
                . 'lunonlu_message_id = 0 '
                . 'WHERE lunonlu_utilisateur_id = :id');
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();
        }
    }

    public static function MarquerSujetLu($sujet, $lu = true)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        if ($lu) {
            // Récupérer le dernier message
            $stmt = $dbh->prepare('SELECT sujet_dernier_message '
                . 'FROM zcov2_forum_sujets '
                . 'WHERE sujet_id = :sujet');
            $stmt->bindParam('sujet', $sujet);
            $stmt->execute();
            $dernierMessage = $stmt->fetchColumn();

            if (!$dernierMessage) {
                return; // Ne devrait pas arriver - le sujet n'existe pas
            }

            $stmt = $dbh->prepare('INSERT INTO zcov2_forum_lunonlu '
                . '(lunonlu_utilisateur_id, lunonlu_sujet_id, lunonlu_message_id) '
                . 'VALUES(:id, :sujet, :message)');
            $stmt->bindParam('id', $_SESSION['id']);
            $stmt->bindParam('sujet', $sujet);
            $stmt->bindParam('message', $dernierMessage);
            $stmt->catchErrors(false);
            $stmt->execute();
            if ($stmt->errorCode() == 23000) // Duplicate record
            {
                $stmt = $dbh->prepare('UPDATE zcov2_forum_lunonlu '
                    . 'SET lunonlu_message_id = :message '
                    . 'WHERE lunonlu_sujet_id = :sujet AND '
                    . 'lunonlu_utilisateur_id = :id');
                $stmt->bindParam('id', $_SESSION['id']);
                $stmt->bindParam('sujet', $sujet);
                $stmt->bindParam('message', $dernierMessage);
                $stmt->execute();
            }
        } else {
            $stmt = $dbh->prepare('DELETE FROM zcov2_forum_lunonlu '
                . 'WHERE lunonlu_sujet_id = :sujet AND '
                . 'lunonlu_utilisateur_id = :id');
            $stmt->bindParam('id', $_SESSION['id']);
            $stmt->bindParam('sujet', $sujet);
            $stmt->execute();
        }
    }

    public static function MarquerDernierMessageLu($message_id, $sujet_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
            SET lunonlu_message_id = :message_id
            WHERE lunonlu_sujet_id = :sujet_id AND lunonlu_utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':message_id', $message_id);
        $stmt->bindParam(':sujet_id', $sujet_id);
        $stmt->bindParam(':utilisateur_id', $_SESSION['id']);
        $stmt->execute();
    }

    public static function DerniereLecture($id)
    {
        if (!verifier('connecte')) {
            return 0;
        }

        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare("SELECT UNIX_TIMESTAMP(utilisateur_derniere_lecture) AS utilisateur_derniere_lecture 
            FROM zcov2_utilisateurs 
            WHERE utilisateur_id = :user_id");
        $stmt->bindParam(':user_id', $id);
        $stmt->execute();
        $resultat = $stmt->fetch(\PDO::FETCH_OBJ);

        return $resultat->utilisateur_derniere_lecture;
    }

}