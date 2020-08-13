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
 * Contrôleur gérant la validation d'un vote à un sondage et son insertion
 * dans la base de données.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class VoterAction extends Controller
{
	public function execute()
	{
	    if (!verifier('sondages_voter')) {
            throw new AccessDeniedHttpException();
        }

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$question = Doctrine_Core::getTable('SondageQuestion')->find($_GET['id']);
			if ($question == false)
				return redirect(11, 'index.html', MSG_ERROR);

			$sondage = $question->Sondage;
			$a_vote = $question->aVote($_SESSION['id'], $this->get('request')->getClientIp(true));

			if (($sondage['ouvert'] && verifier('sondages_voir')) || verifier('sondages_voir_caches'))
			{
				if (isset($_POST['voter']) XOR isset($_POST['blanc']))
				{
					//Le sondage doit être ouvert pour voter.
					if (!$sondage->estOuvert())
					{
						return redirect(6, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
					}
					//Interdiction de voter deux fois.
					elseif ($a_vote)
					{
						return redirect(5, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
					}
					else
					{
						if (isset($_POST['voter']))
						{
							if (!$question['libre'])
							{
								//On doit respecter le nombre de choix indiqué.
								if (empty($_POST['reponse']) || count($_POST['reponse']) > $question['nb_max_choix'] || count($_POST['reponse']) < $question['nb_min_choix'])
								{
									return redirect(15, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
								}
								foreach ($_POST['reponse'] as $rep)
								{
									$reponse = Doctrine_Core::getTable('SondageReponse')->find($rep);
									if ($reponse == false || $reponse['question_id'] != $question['id'])
										return redirect(12, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);

									$vote = new SondageVote;
									$vote['utilisateur_id'] = $_SESSION['id'] > 0 ? $_SESSION['id'] : null;
									$vote['question_id']    = $question['id'];
									$vote['reponse_id']     = $reponse['id'];
									$vote['date']           = new Doctrine_Expression('NOW()');
									$vote['ip']             = ip2long($this->get('request')->getClientIp(true));
									$vote->save();

									$reponse['nb_votes'] += 1;
									$reponse->save();
								}
							}
							else
							{
								$reponse = trim($_POST['reponse']);
								if (empty($reponse))
									return redirect(16, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);

								$vote = new SondageVote;
								$vote['utilisateur_id'] = $_SESSION['id'] > 0 ? $_SESSION['id'] : null;
								$vote['question_id']    = $question['id'];
								$vote['date']           = new Doctrine_Expression('NOW()');
								$vote['ip']             = ip2long($this->get('request')->getClientIp(true));
								$vote->save();

								$texte = new SondageTexte;
								$texte['vote_id']       = $vote['id'];
								$texte['texte']         = $reponse;
								$texte->save();
							}

							$question['nb_votes'] = $question['nb_votes'] + 1;
							$question->save();
						}
						else
						{
							//Une question obligatoire n'autorise pas le vote blanc.
							if ($question['obligatoire'])
							{
								return redirect(14, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
							}
							$vote = new SondageVote;
							$vote['utilisateur_id'] = $_SESSION['id'] > 0 ? $_SESSION['id'] : null;
							$vote['question_id']    = $question['id'];
							$vote['reponse_id']     = null;
							$vote['date']           = new Doctrine_Expression('NOW()');
							$vote['ip']             = ip2long($this->get('request')->getClientIp(true));
							$vote->save();

							$question['nb_blanc'] = $question['nb_blanc'] + 1;
							$question->save();

							$reponse = array('question_suivante' => 'suivante');
						}

						//Redirection vers la question suivante.
						if (!$question['libre'] && $reponse['question_suivante'] == 'fin')
						{
							$suivante = null;
						}
						elseif ($question['libre'] || $reponse['question_suivante'] == 'suivante')
						{
							$suivante = $question->getQuestionSuivante();
						}
						else
						{
							$suivante = $reponse['question_suivante_id'];
						}

						//Si le sondage est terminé, on redirige vers la première question
						//pour voir les résultats.
						if ($suivante == null)
						{
							return redirect(4, 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html');
						}
						//Sinon on continue.
						else
						{
							return new Symfony\Component\HttpFoundation\RedirectResponse('sondage-'.$sondage['id'].'-'.$suivante.'-'.rewrite($sondage['nom']).'.html');
						}
					}
				}
				else
					return redirect(12, 'sondage-'.$sondage['id'].'-'.$question['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}