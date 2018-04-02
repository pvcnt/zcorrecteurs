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
                                ($InfosRecrutement['recrutement_etat'] == RECRUTEMENT_CACHE && !verifier('recrutements_editer') && !verifier('recrutements_voir_candidatures') && !verifier('recrutements_repondre'))
                        )
                                throw new NotFoundHttpException();

			$InfosCandidature = InfosCandidature($_SESSION['id'], $_GET['id']);
			$quiz = $this->get('zco_quiz.manager.quiz')->get($InfosRecrutement['recrutement_id_quiz']);

			if ( !$quiz ||
			     empty($InfosRecrutement['recrutement_id_quiz']) ||
			     $InfosCandidature['candidature_quiz_score'] !== NULL ||
			     $InfosCandidature['candidature_etat'] != CANDIDATURE_REDACTION)
			{
				throw new NotFoundHttpException();
			}

			if (!$quiz)
				throw new NotFoundHttpException('Le recrutement n\'a pas de quiz associé.');

			DebutQuiz($InfosCandidature);

			Page::$titre = htmlspecialchars($InfosRecrutement['recrutement_nom']).' - Répondre au quiz';
			$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
			
			return render_to_response(compact('quiz', 'InfosCandidature', 'quiz', 'InfosCandidature', 'InfosRecrutement'));
		}
        throw new NotFoundHttpException();
	}
}
