<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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
 * Contrôleur pour l'affichage du rapport d'activité des zCorrecteurs.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class ActiviteAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Rapport d\'activité des zCorrecteurs';

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/rapport_activite.php');

		//On récupère les infos générales sur la zCorrection
		$donneesTutos = RecupDonneesTutos();

		//On sépare en 5 arrays pour avoir la structure numérique du premier graphe (de 4 courbes)
		$nombre_total_corrections_news = $donneesTutos['nombre_total_corrections_news'];
		$nombre_total_recorrections_news = $donneesTutos['nombre_total_recorrections_news'];
		$nombre_total_corrections_mini = $donneesTutos['nombre_total_corrections_mini'];
		$nombre_total_recorrections_mini = $donneesTutos['nombre_total_recorrections_mini'];
		$nombre_total_corrections_big = $donneesTutos['nombre_total_corrections_big'];
		$nombre_total_recorrections_big = $donneesTutos['nombre_total_recorrections_big'];

		//On récupère les informations par zCorrecteur (pour tracer le second graphe)
		$donneesTutosParZcorr = RecupDonneesTutosParZcorr();

		//On envoie le tout au grapheur par session
		//Graphique 1
		$_SESSION['nombre_total_corrections_news'] = $nombre_total_corrections_news;
		$_SESSION['nombre_total_recorrections_news'] = $nombre_total_recorrections_news;
		$_SESSION['nombre_total_corrections_mini'] = $nombre_total_corrections_mini;
		$_SESSION['nombre_total_recorrections_mini'] = $nombre_total_recorrections_mini;
		$_SESSION['nombre_total_corrections_big'] = $nombre_total_corrections_big;
		$_SESSION['nombre_total_recorrections_big'] = $nombre_total_recorrections_big;

		//Graphique 2
		$_SESSION['donneesTutosParZcorr'] = $donneesTutosParZcorr;

		//Inclusion de la vue
		fil_ariane('Rapport d\'activité des zCorrecteurs');
		return render_to_response(array());
	}
}
