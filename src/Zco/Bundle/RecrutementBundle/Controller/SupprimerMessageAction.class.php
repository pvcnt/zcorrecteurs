<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la suppression d'un message de la shoutbox des
 * administrateurs.
 *
 * @author Vanger
 */
class SupprimerMessageAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/candidatures.php');
        include_once(__DIR__.'/../modeles/recrutements.php');

        if (!verifier('recrutements_ecrire_shoutbox')) {
            throw new AccessDeniedHttpException();
        }
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			include(__DIR__.'/../modeles/commentaires.php');

			$InfosCommentaire = InfosCommentaire($_GET['id']);
			if(empty($InfosCommentaire))
				throw new NotFoundHttpException();

			if(!($InfosCommentaire['recrutement_etat'] != RECRUTEMENT_FINI &&
			     verifier('recrutements_voir_shoutbox')) &&
			   !($InfosCommentaire['recrutement_etat'] == RECRUTEMENT_FINI &&
			     verifier('recrutements_termines_voir_shoutbox')))
				throw new AccessDeniedHttpException();

			if(isset($_POST['cancel']))
			{
				return new RedirectResponse('candidature-'.$InfosCommentaire['commentaire_candidature_id'].'.html');
			}

			if(isset($_POST['submit']))
			{
				SupprimerCommentaireShoutbox($_GET['id']);
				return redirect(
				    'Le message a bien été supprimé.',
                    'candidature-'.$InfosCommentaire['commentaire_candidature_id'].'.html'
                );
			}

			$InfosCandidature = InfosCandidature($InfosCommentaire['commentaire_candidature_id']);
			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']) => 'candidature-'.$InfosCommentaire['commentaire_candidature_id'].'.html',
				'Supprimer un commentaire'
			));
			return render_to_response(array('InfosCommentaire' => $InfosCommentaire));
		}
		else
            throw new NotFoundHttpException();
	}
}
