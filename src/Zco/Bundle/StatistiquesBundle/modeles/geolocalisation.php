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

/**
 * Modèle récupérant les statistiques relatives à la géolocalisation des membres.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 22 mai 2009
 * @last 22 mai 2009 vincent1870
 */

function RecupStatistiquesGeolocalisation()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_utilisateurs " .
			"WHERE utilisateur_localisation NOT IN('-', '0', 'Inconnu', '')");
	$stmt->execute();
	$nb_utilisateurs = $stmt->fetchColumn();
	$stmt->closeCursor();

	$stmt = $dbh->prepare("SELECT COUNT(*) AS nb, utilisateur_localisation " .
			"FROM zcov2_utilisateurs " .
			"WHERE utilisateur_localisation NOT IN('-', '0', 'Inconnu', '') " .
			"GROUP BY utilisateur_localisation " .
			"ORDER BY COUNT(*) DESC");
	$stmt->execute();
	$donnees = $stmt->fetchAll();
	$retour_graph = $retour_tableau = array();

	foreach($donnees as $data)
	{
		$pourcent = round(100*$data['nb'] / $nb_utilisateurs, 1);
		$retour_tableau[$data['utilisateur_localisation']] = $pourcent;
		if($pourcent >= 1)
			$retour_graph[$data['utilisateur_localisation']] = $pourcent;
		else
		{
			if(!isset($retour_graph['Autres']))
				$retour_graph['Autres'] = $pourcent;
			else
				$retour_graph['Autres'] += $pourcent;
		}
	}

	return array($retour_graph, $retour_tableau, $nb_utilisateurs);
}
