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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'ajout d'un message dans la shoutbox des administrateurs.
 *
 * @author Vanger
 */
class AjouterMessageAction extends Controller
{
	public function execute()
	{
        if (!verifier('recrutements_ecrire_shoutbox')) {
            throw new AccessDeniedHttpException();
        }

		zCorrecteurs::VerifierFormatageUrl(null, true, true);
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				return redirect(227, '/recrutement/', MSG_ERROR);

			if(!($InfosCandidature['recrutement_etat'] != RECRUTEMENT_FINI &&
			     verifier('recrutements_voir_shoutbox')) &&
			   !($InfosCandidature['recrutement_etat'] == RECRUTEMENT_FINI &&
			     verifier('recrutements_termines_voir_shoutbox')))
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

			include(dirname(__FILE__).'/../modeles/commentaires.php');

			if(!empty($_GET['id2']))
			{
				list($texte_zform, $auteur) = RecupererZCodeCommentaire($_GET['id2']);
				$texte_zform = (!empty($texte_zform)) ? '<citation nom="'.$auteur.'">'.$texte_zform.'</citation>' : '';
			}
			else
				$texte_zform = '';

			if(isset($_POST['submit']))
			{
				$_POST['texte'] = trim($_POST['texte']);
				if(empty($_POST['texte']))
					return redirect(17, 'ajouter-message-'.$_GET['id'].'.html', MSG_ERROR);

				$new_com = AjouterCommentaireShoutbox($_GET['id'], $_POST['texte']);
				return redirect(348, 'candidature-'.$_GET['id'].'-'.$new_com.'.html');
			}

			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']) => 'candidature-'.$_GET['id'].'.html',
				'Ajouter un commentaire'
			));
			
			return render_to_response(array(
				'InfosCandidature' => $InfosCandidature,
				'texte_zform' => $texte_zform,
			));
		}
		else
			return redirect(226, 'index.html', MSG_ERROR);
	}
}
