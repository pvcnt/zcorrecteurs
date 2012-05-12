<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

function StatsGeneralesForumMessages()
{
	if (false === ($res = Container::getService('zco_core.cache')->Get('forum_statistiques_temporelles_messages'))) {
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
		Container::getService('zco_core.cache')->Set('forum_statistiques_temporelles_messages', $res, 86400);
	}
	return $res;
}

function StatsGeneralesForumSujets()
{
	if (false === ($res = Container::getService('zco_core.cache')->Get('forum_statistiques_temporelles_sujets'))) {
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
		Container::getService('zco_core.cache')->Set('forum_statistiques_temporelles_sujets', $res, 86400);
	}
	return $res;
}