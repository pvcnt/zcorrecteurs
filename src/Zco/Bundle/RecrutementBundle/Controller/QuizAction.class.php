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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Réponse à un quiz de recrutement.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class QuizAction extends Controller
{
	public function execute()
	{
	    include_once(__DIR__.'/../modeles/quiz.php');
	    
		//Si on a bien envoyé un recrutement
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosRecrutement = InfosRecrutement($_GET['id']);
                        if(
                                empty($InfosRecrutement) ||
                                ($InfosRecrutement['recrutement_prive'] && !verifier('recrutements_voir_prives')) ||
                                ($InfosRecrutement['recrutement_etat'] == RECRUTEMENT_CACHE && !verifier('recrutements_ajouter') && !verifier('recrutements_editer') && !verifier('recrutements_supprimer') && !verifier('recrutements_voir_candidatures') && !verifier('recrutements_repondre'))
                        )
                                return redirect(229, '/recrutement/', MSG_ERROR);

			$InfosCandidature = InfosCandidature($_SESSION['id'], $_GET['id']);
			$quiz = Doctrine_Core::getTable('Quiz')->find($InfosRecrutement['recrutement_id_quiz']);

			if ( !$quiz ||
			     empty($InfosRecrutement['recrutement_id_quiz']) ||
			     $InfosCandidature['candidature_quiz_score'] !== NULL ||
			     $InfosCandidature['candidature_etat'] != CANDIDATURE_REDACTION)
			{
				throw new NotFoundHttpException();
			}

			zCorrecteurs::VerifierFormatageUrl($InfosRecrutement['recrutement_nom'], true);

			if (!$quiz)
				throw new NotFoundHttpException('Le recrutement n\'a pas de quiz associé.');

			DebutQuiz($InfosCandidature);

			Page::$titre = htmlspecialchars($InfosRecrutement['recrutement_nom']).' - Répondre au quiz';
			$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
			
			return render_to_response(compact('quiz', 'InfosCandidature', 'quiz', 'InfosCandidature', 'InfosRecrutement'));
		}
		return redirect(228, 'index.html', MSG_ERROR);
	}
}
