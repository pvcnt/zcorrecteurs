<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
 * Contrôleur gérant la modification d'une question d'un sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierQuestionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$question = Doctrine_Core::getTable('SondageQuestion')->find($_GET['id']);
			if ($question == false)
				return redirect(11, 'index.html', MSG_ERROR);

			if (verifier('sondages_editer') || ($question->Sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_editer_siens')))
			{
				//Ordonnancement plus pratique des réponses possibles.
				$tmp = $question->Reponses;
				$reponses = array();
				foreach ($tmp as $rep)
				{
					$reponses[$rep['ordre']] = $rep;
				}
				unset($tmp);

				//Modification d'une question demandée.
				if (!empty($_POST['texte']))
				{
					$question['nom']               = $_POST['texte'];
					$question['nb_min_choix']      = $_POST['nb_min_choix'];
					$question['nb_max_choix']      = $_POST['nb_max_choix'];
					$question['libre']             = isset($_POST['libre']);
					$question['obligatoire']       = !isset($_POST['obligatoire']);
					$question['resultats_publics'] = isset($_POST['resultats_publics']);
					if (isset($_POST['raz_votes']))
					{
						$question['nb_votes'] = 0;
						$question['nb_blanc'] = 0;

						Doctrine_Query::create()
							->delete('SondageVote')
							->where('question_id = ?', $question['id'])
							->execute();
					}

					for ($i = 1 ; $i <= 10 ; $i++)
					{
						if (!empty($_POST['reponse_'.$i]))
						{
							if (isset($reponses[$i]))
							{
								$reponse = $reponses[$i];
								if (isset($_POST['raz_votes']))
								{
									$reponse['nb_votes'] = 0;
								}
							}
							else
							{
								$reponse = new SondageReponse;
								$reponse['question_id'] = $question['id'];
								$reponse['ordre'] = $i;
							}

							$reponse['nom'] = $_POST['reponse_'.$i];
							$reponse['question_suivante'] = is_numeric($_POST['question_suivante_'.$i]) ? 'id' : ($_POST['question_suivante_'.$i] == 'fin' ? 'fin' : 'suivante');
							$reponse['question_suivante_id'] = $reponse['question_suivante'] == 'id' ? $_POST['question_suivante_'.$i] : null;
							$reponse->save();
							$reponse = null;
						}
						elseif (isset($reponses[$i]))
						{
							$reponse = $reponses[$i];
							$question['nb_votes'] = $question['nb_votes'] - $reponse['nb_votes'];
							$reponse->delete();
						}
					}
					$question->save();

					return redirect(9, 'modifier-'.$question['sondage_id'].'.html');
				}
				fil_ariane(array(
					'Gestion des sondages' => 'gestion.html',
					htmlspecialchars($question->Sondage['nom']) => 'sondage-'.$question['sondage_id'].'-'.rewrite($question->Sondage['nom']).'.html',
					'Modifier une question',
				));
				
				return render_to_response(array(
					'question'  => $question,
					'sondage'   => $question->Sondage,
					'questions' => $question->Sondage->Questions,
					'reponses'  => $reponses,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}