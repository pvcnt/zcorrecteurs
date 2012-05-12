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
 * Contrôleur gérant l'affichage de la fiche descriptive d'un tutoriel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FicheTutoAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre .= ' - Fiche d\'un tutoriel';

		$s = InfosCorrection($_GET['id']);

		if(
			//Droit par autorisation de voir tels tutos
			(
				((empty($s['correction_id'])|| (empty($s['recorrection_id']) && $s['soumission_recorrection'] == 1)) && verifier('voir_tutos_attente'))
				|| (((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)) && verifier('voir_tutos_corriges'))
				|| (((!empty($s['correction_id']) && empty($s['correction_date_fin'])) || (!empty($s['recorrection_id']) && empty($s['recorrection_date_fin']))) && verifier('voir_tutos_correction'))
			)
			||
			//Droit par correction de ce tuto
			(
				($s['id_correcteur'] == $_SESSION['id'] || $s['id_recorrecteur'] == $_SESSION['id']) && verifier('zcorriger')
			)
		)
		{
			if (empty($_GET['id']) || !is_numeric($_GET['id']))
			{
				return redirect(138, '/', MSG_ERROR);
			}
			else
			{
				if(empty($s))
				{
					return redirect(139, '/', MSG_ERROR);
				}
				else
				{
					//Inclusion de la vue
					fil_ariane(array(
						$s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']) => 'fiche-tuto-'.$_GET['id'].'.html',
						'Voir la fiche du tutoriel'
					));
					$this->get('zco_vitesse.resource_manager')->requireResources(array(
        			    '@ZcoCoreBundle/Resources/public/css/zcode.css',
            		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
            		));
            		
					return render_to_response(array('s' => $s));
				}
			}
		}
		else
		{
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
	}
}
