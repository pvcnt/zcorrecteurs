<?php


namespace Zco\Bundle\ForumBundle\Domain;

/**
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
final class PollDAO
{
    public static function ListerResultatsSondage($sondage_id)
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();

        // Votes normaux
        $stmt = $dbh->prepare("
	SELECT choix_id, choix_texte, COUNT(vote_choix) AS nombre_votes
	FROM zcov2_forum_sondages_choix
	LEFT JOIN zcov2_forum_sondages_votes ON vote_choix = choix_id
	WHERE choix_sondage_id = :sondage
	GROUP BY choix_id
	ORDER BY choix_id ASC
	");
        $stmt->bindParam(':sondage', $sondage_id);
        $stmt->execute();
        $retour = $stmt->fetchAll();
        $stmt->closeCursor();

        // Votes blancs
        $stmt = $dbh->prepare("
	SELECT COUNT(vote_choix) AS nombre_votes
	FROM zcov2_forum_sondages_votes
	WHERE vote_sondage_id = :sondage AND vote_choix = 0
	");
        $stmt->bindParam(':sondage', $sondage_id);
        $stmt->execute();
        $retour[] = array('nombre_votes'=>$stmt->fetchColumn(), 'choix_id'=>0, 'choix_texte'=>'Vote blanc');
        $stmt->closeCursor();

        return $retour;
    }
}