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

/**
 * Contrôleur gérant l'affichage des statistiques temporelles
 * d'utilisation du forum.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 * @todo		Changer le droit associé (cf security.yml).
 */
class StatistiquesTemporellesAction extends ForumActions
{
	public function execute()
	{
		Page::$titre .= ' - Statistiques d\'utilisation du forum';
		zCorrecteurs::VerifierFormatageUrl();

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/statistiques.php');

		$mois = array(
			1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
			'juillet', 'août', 'septembre', 'octobre', 'novembre','décembre'
		);

		$StatsGeneralesForumMessages = StatsGeneralesForumMessages();
		$StatsGoogleMessages = array();
		foreach($StatsGeneralesForumMessages as $k => $q) {
			$StatsGoogleMessages[] = array(ucfirst($mois[$q['mois']]).' '.$q['annee'], (int)$q['count_team'], (int)($q['count_total']-$q['count_team']));
		}

		$StatsGeneralesForumSujets = StatsGeneralesForumSujets();
		$StatsGoogleSujets = array();
		foreach($StatsGeneralesForumSujets as $k => $q) {
			$StatsGoogleSujets[] = array(ucfirst($mois[$q['mois']]).' '.$q['annee'], (int)$q['count']);
		}

		// Inclusion de la vue
		fil_ariane('Statistiques générales d\'utilisation du forum');
		return render_to_response(array(
			'StatsGoogleSujets' => $StatsGoogleSujets,
			'StatsGoogleMessages' => $StatsGoogleMessages,

		));
	}
}
