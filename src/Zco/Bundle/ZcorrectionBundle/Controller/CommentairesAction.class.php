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
 * Contrôleur gérant les commentaires apportés à un tutoriel.
 *
 * @author Savageman, vincent1870, DJ Fox
 */
class CommentairesAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		//Si on n'a pas envoyé de tutoriel
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(138, '', MSG_ERROR);
		}
		else
		{
			//On récupère les infos sur la correction
			$s = InfosCorrection($_GET['id']);

			//Si le tutoriel n'existe pas
			if (empty($s))
			{
				return redirect(139, '', MSG_ERROR);
			}
			//Si on n'a pas le droit de corriger ce tutoriel
			else if(((!$s['soumission_recorrection'] && $s['id_correcteur'] != $_SESSION['id']) || ($s['soumission_recorrection'] && $s['id_recorrecteur'] != $_SESSION['id'])) && !verifier('zcorrection_editer_tutos'))
			{
				return redirect(139, '', MSG_ERROR);
			}
			else
			{
				//Si on veut mettre à jour les commentaires
				if (isset($_POST['maj']))
				{
					(!empty($s['recorrection_id'])) ? MettreAJourCorrection($s['recorrection_id']) : MettreAJourCorrection($s['correction_id']);
					MettreAJourSoumission($_GET['id']);
					return redirect(161, 'commentaires-'.$_GET['id'].'.html'.(isset($_GET['cid']) ? '?cid' : ''));
				}
			}

			$confidentialite = $s['recorrection_id'] ? $s['correcteur_invisible_recorrection'] : $s['correcteur_invisible_correction'];
			$commentaire = $s['recorrection_id'] ? $s['commentaire_recorrection'] : $s['commentaire_correction'];
			$commentaire_valido = $s['recorrection_id'] ? $s['commentaire_valido_recorrection'] : $s['commentaire_valido_correction'];
			$commentaire2 = $s['soumission_commentaire'];
			$type = (MINI_TUTO == $s['soumission_type_tuto']) ? 'mini' : 'big';

			//Inclusion de la vue
			fil_ariane(array(
				$s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']) => 'fiche-tuto-'.$_GET['id'].'.html',
				'Modifier les commentaires'
			));
			$this->get('zco_vitesse.resource_manager')->requireResource(
    		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css'
    		);
    		
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
