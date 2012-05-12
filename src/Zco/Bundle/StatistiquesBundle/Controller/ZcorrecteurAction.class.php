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
 * Contrôleur pour l'affichage des statistiques de zCorrection par zCorrecteur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcorrecteurAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		//Détermination de l'id du zCorrecteur
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
			$id = $_GET['id'];
		else
			$id = $_SESSION['id'];

		if(($id == $_SESSION['id'] && verifier('stats_zcorrecteur')) || verifier('stats_zcorrecteurs'))
		{
			//Inclusion du modèle
			include(dirname(__FILE__).'/../modeles/zcorrecteur.php');

			$Stats = RecupStatistiquesZcorrecteur($id);
			Page::$titre = 'Statistiques de zCorrection de '.htmlspecialchars($Stats['infos']['utilisateur_pseudo']);

			//Modifications
			if(empty($Stats['temps_moyen_correction_global']))
				$Stats['temps_moyen_correction_global'] = '00h00';
			if(empty($Stats['temps_moyen_correction_mini']))
				$Stats['temps_moyen_correction_mini'] = '00h00';
			if(empty($Stats['temps_moyen_correction_big']))
				$Stats['temps_moyen_correction_big'] = '00h00';
			if(empty($Stats['temps_moyen_recorrection_global']))
				$Stats['temps_moyen_recorrection_global'] = '00h00';
			if(empty($Stats['temps_moyen_recorrection_mini']))
				$Stats['temps_moyen_recorrection_mini'] = '00h00';
			if(empty($Stats['temps_moyen_recorrection_big']))
				$Stats['temps_moyen_recorrection_big'] = '00h00';

			//Inclusion de la vue
			fil_ariane('Statistiques d\'un zCorrecteur');
			
			return render_to_response(array('Stats' => $Stats));
		}
		else
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	}
}
