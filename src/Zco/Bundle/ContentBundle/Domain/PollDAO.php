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

namespace Zco\Bundle\ContentBundle\Domain;

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