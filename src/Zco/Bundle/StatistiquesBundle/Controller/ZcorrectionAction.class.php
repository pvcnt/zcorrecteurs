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
 * Contrôleur pour l'affichage des statistiques globales.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class ZcorrectionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Statistiques de zCorrection';

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/statistiques.php');

		//On récupère les stats.
		$Stats = RecupStatistiques();

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
		function trans_heure($heure_depart)
		{
			if(empty($heure_depart))
			{
				$heure_depart = '00h00';
			}
			$heures = strtok($heure_depart, 'h');
			$minutes = strtok(':');
			return ($minutes*60 + $heures*3600)/3600;
		}
		$sec_tmcg = trans_heure($Stats['temps_moyen_correction_global']);
		$sec_tmcm = trans_heure($Stats['temps_moyen_correction_mini']);
		$sec_tmcb = trans_heure($Stats['temps_moyen_correction_big']);
		$sec_tmrg = trans_heure($Stats['temps_moyen_recorrection_global']);
		$sec_tmrm = trans_heure($Stats['temps_moyen_recorrection_mini']);
		$sec_tmrb = trans_heure($Stats['temps_moyen_recorrection_big']);

		//Inclusion de la vue
		fil_ariane('Statistiques de zCorrection');
		return render_to_response(get_defined_vars());
	}
}
