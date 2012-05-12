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
 * Contrôleur pour l'affichage du rapport d'activité des développeurs.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class DeveloppementAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Rapport d\'activité des développeurs';

		//Inclusion des modèles
		include(__DIR__.'/../modeles/developpement.php');

		//Récupération des données
		$annee = !empty($_GET['annee']) && is_numeric($_GET['annee']) ? $_GET['annee'] : (int)date('Y');
		$mois = !empty($_GET['mois']) && is_numeric($_GET['mois']) ? $_GET['mois'] : (int)date('m');
		$donnees = RecupRapportDeveloppeursAnnee($annee);
		$donnees_tableau = RecupRapportDeveloppeursMois($annee, $mois);

		$_SESSION['annee'] = $annee;
		$_SESSION['donnees_resolution_demandes'] = $donnees;

		//Inclusion de la vue
		fil_ariane('Rapport d\'activité des développeurs');
		
		return render_to_response(array(
			'mois' => $mois,
			'annee' => $annee,
			'donnees_tableau' => $donnees_tableau,
		));
	}
}
