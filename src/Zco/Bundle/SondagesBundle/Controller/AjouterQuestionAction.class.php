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
 * Contrôleur gérant l'ajout d'une question à un sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterQuestionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$sondage = Doctrine_Core::getTable('Sondage')->find($_GET['id']);
			if ($sondage == false)
				return redirect(7, 'index.html', MSG_ERROR);

			if (verifier('sondages_editer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_editer_siens')))
			{
				//Ajout d'une question demandé.
				if (!empty($_POST['texte']))
				{
					$question = new SondageQuestion;
					$question['sondage_id']        = $sondage['id'];
					$question['nom']               = $_POST['texte'];
					$question['nb_min_choix']      = $_POST['nb_min_choix'];
					$question['nb_max_choix']      = $_POST['nb_max_choix'];
					$question['libre']             = isset($_POST['libre']);
					$question['obligatoire']       = !isset($_POST['obligatoire']);
					$question['resultats_publics'] = isset($_POST['resultats_publics']);
					$question->save();

					$sondage['nb_questions'] = $sondage['nb_questions']+1;
					$sondage->save();

					for ($i = 1 ; $i <= 10 ; $i++)
					{
						if (!empty($_POST['reponse_'.$i]))
						{
							$reponse = new SondageReponse;
							$reponse['question_id'] = $question['id'];
							$reponse['nom'] = $_POST['reponse_'.$i];
							$reponse['ordre'] = $i;
							$reponse['question_suivante'] = is_numeric($_POST['question_suivante_'.$i]) ? 'id' : ($_POST['question_suivante_'.$i] == 'fin' ? 'fin' : 'suivante');
							$reponse['question_suivante_id'] = $reponse['question_suivante'] == 'id' ? $_POST['question_suivante_'.$i] : null;
							$reponse->save();
							$reponse = null;
						}
					}

					return redirect(8, 'modifier-'.$sondage['id'].'.html');
				}

				fil_ariane(array(
					'Gestion des sondages' => 'gestion.html',
					htmlspecialchars($sondage['nom']) => 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html',
					'Ajouter une question',
				));
				
				return render_to_response(array(
					'sondage'   => $sondage,
					'questions' => $sondage->Questions,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}