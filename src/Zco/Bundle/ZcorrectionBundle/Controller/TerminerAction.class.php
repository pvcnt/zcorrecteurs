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
 * Contrôleur pour la fin de correction d'un tutoriel.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 */
class TerminerAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre .= ' - Terminer la correction d\'un tutoriel';

		//Si aucun tuto n'a été envoyé
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(138, '/zcorrection/', MSG_ERROR);
		}
		else
		{
			//On récupère des infos sur la correction
			$s = InfosCorrection($_GET['id']);

			//Si le tutoriel n'existe pas
			if (empty($s))
			{
				return redirect(139, '/zcorrection/', MSG_ERROR);
			}
			//Si on n'a pas le droit de corriger ce tutoriel
			else if(((!$s['soumission_recorrection'] && $s['id_correcteur'] != $_SESSION['id']) || ($s['soumission_recorrection'] && $s['id_recorrecteur'] != $_SESSION['id'])) && !verifier('zcorrection_editer_tutos'))
			{
				return redirect(139, '/zcorrection/', MSG_ERROR);
			}
			else if(empty($s['correction_id']) OR empty($s['correction_date_debut']))
			{
				return redirect(139, '/zcorrection/', MSG_ERROR);
			}
			else
			{
				//Si on a fini la (re)correction
				if (isset($_POST['correction']))
				{
					if (!$s['recorrection_id'] && isset($_POST['recorrection']))
					{
						//Fin de la correction
						$besoinRecorrection = 1;
						TerminerCorrection($s['correction_id'], 1, $besoinRecorrection);
						//DemanderRecorrection($_GET['id']);
						return redirect(150, '/zcorrection/');
					}
					else
					{
						//TODO : vérifier que cela marche, sensible
						return new Symfony\Component\HttpFoundation\RedirectResponse('/zcorrection/exporter-sdz.html?id='.$_GET['id']);
					}
				}
				$commentaire = !empty($s['recorrection_id']) ? $s['commentaire_recorrection'] : $s['commentaire_correction'];
				$commentaire_valido = $s['recorrection_id'] ? $s['commentaire_valido_recorrection'] : $s['commentaire_valido_correction'];
				$commentaire2 = $s['soumission_commentaire'];
				$confidentialite = !empty($s['recorrection_id']) ? $s['correcteur_invisible_recorrection'] : $s['correcteur_invisible_correction'];

				//Inclusion de la vue
				fil_ariane(array(
					$s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']) => 'fiche-tuto-'.$_GET['id'].'.html',
					'Terminer la correction'
				));
				$this->get('zco_vitesse.resource_manager')->requireResources(array(
    			    '@ZcoCoreBundle/Resources/public/css/zcode.css'
        		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
        		));
        		
				return render_to_response(array(
					's' => $s,
					'confidentialite' => $confidentialite,
					'commentaire' => $commentaire,
					'commentaire2' => $commentaire2,
					'commentaire_valido' => $commentaire_valido,
				));
			}
		}
	}
}
