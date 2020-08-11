<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la modification d'une candidature (texte de motivation,
 * rédaction, test, état).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>, Vanger
 */
class EditerCandidatureAction extends Controller
{
	public function execute()
	{
		//Si on a bien envoyé une candidature
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				return redirect(227, '/recrutement/', MSG_ERROR);
			zCorrecteurs::VerifierFormatageUrl($InfosCandidature['candidature_pseudo'], true);

			if($InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION)
				return redirect(4, 'candidature-'.$_GET['id'].'.html', MSG_ERROR);

			//Si on a envoyé l'édition
			if(!empty($_POST['motiv']))
			{
				EditerCandidature($_GET['id']);
				return redirect(235, 'candidature-'.$_GET['id'].'.html');
			}

			//Inclusion de la vue
			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']) => 'candidature-'.$_GET['id'].'.html',
				'Modifier la candidature'
			));
			
			return render_to_response(array(
				'InfosCandidature' => $InfosCandidature,
			));
		}
		else
			return redirect(226, '/recrutement/', MSG_ERROR);
	}
}
