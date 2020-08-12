<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Doctrine\Common\Cache\Cache;

function StatsGeneralesForumMessages(Cache $cache)
{
	if (false === ($res = $cache->fetch('forum_statistiques_temporelles_messages'))) {
		$dbh = Doctrine_Manager::connection()->getDbh();
		$res = $dbh->query("
		SELECT SUM(nb) AS count_total, SUM(team) as count_team, mois, annee FROM (
			SELECT COUNT(*) AS nb, SUM(groupe_team) as team, MONTH(`message_date`) as mois, YEAR(`message_date`) as annee, COALESCE(groupe_team, 0) AS groupe_team
			FROM zcov2_forum_messages m
			LEFT JOIN zcov2_utilisateurs u ON m.message_auteur = u.utilisateur_id
			LEFT JOIN zcov2_groupes g ON u.utilisateur_id_groupe = g.groupe_id
			GROUP BY annee, mois
		) alias
		GROUP BY annee, mois
		ORDER BY annee ASC, mois ASC");
		$res = $res->fetchAll();
		$cache->save('forum_statistiques_temporelles_messages', $res, 86400);
	}
	return $res;
}

function StatsGeneralesForumSujets(Cache $cache)
{
	if (false === ($res = $cache->fetch('forum_statistiques_temporelles_sujets'))) {
		$dbh = Doctrine_Manager::connection()->getDbh();
		$res = $dbh->query("
		SELECT SUM(nb) AS count, mois, annee FROM (
			SELECT COUNT(*) AS nb, MONTH(`sujet_date`) as mois, YEAR(`sujet_date`) as annee
			FROM zcov2_forum_sujets
			GROUP BY annee, mois
		) alias
		GROUP BY annee, mois
		ORDER BY annee ASC, mois ASC");
		$res = $res->fetchAll();
        $cache->save('forum_statistiques_temporelles_sujets', $res, 86400);
	}
	return $res;
}