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
 * Contrôleur pour les statistiques d'utilisation du flux du blog.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class BlogFluxAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre .= ' - Consultation du flux du blog';

		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/flux.php');

		//Récupération des données
		$annee = !empty($_GET['annee']) && is_numeric($_GET['annee']) ? $_GET['annee'] : (int)date('Y');
		$id_cat = !empty($_GET['categorie']) && is_numeric($_GET['categorie']) ? $_GET['categorie'] : GetIDCategorie('blog');
		$periode = !empty($_GET['periode']) && in_array($_GET['periode'], array('jour', 'mois')) ? $_GET['periode'] : 'mois';
		$donnees = RecupVisitesFluxBlog($annee, $id_cat, ($periode == 'jour' ? 'DAYOFWEEK' : 'MONTH'));
		$NbAbonnes = RecupNbVuesHier();

		$_SESSION['annee'] = $annee;
		$_SESSION['periode'] = $periode;
		$_SESSION['donnees_flux_blog'] = $donnees;

		//Inclusion de la vue
		fil_ariane('Consultation du flux du blog');
		
		return render_to_response(compact(
			'NbAbonnes', 'id_cat', 'annee', 'periode'
		));
	}
}
