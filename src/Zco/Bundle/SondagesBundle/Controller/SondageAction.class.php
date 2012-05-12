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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'affichage d'un sondage. Peut afficher soit le formulaire
 * pour voter à une question précise, et s'occuper de la validation du vote,
 * soit afficher les résultats du vote, si le membre a les droits nécessaires.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SondageAction extends Controller
{
	public function execute()
	{
		//zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$sondage = Doctrine_Core::getTable('Sondage')->find($_GET['id']);
			if ($sondage == false)
				return redirect(7, 'index.html', MSG_ERROR);

			if (($sondage['ouvert'] && verifier('sondages_voir')) || verifier('sondages_voir_caches'))
			{
				//Si saut rapide vers une autre question.
				if (!empty($_POST['saut_rapide']) && is_numeric($_POST['saut_rapide']))
				{
					return new Symfony\Component\HttpFoundation\RedirectResponse('sondage-'.$sondage['id'].'-'.$_POST['saut_rapide'].'.html#question');
				}

				//Optimisation référencement.
				Page::$titre = htmlspecialchars($sondage['nom']);
				$description = strip_tags($sondage['description']);
				if (mb_strlen($description) > 240)
				{
					Page::$description = htmlspecialchars(mb_substr($description, 0, mb_strpos($description, ' ', (mb_strlen($description) > 250 ? 240 : 250))));
				}
				else
				{
					Page::$description = htmlspecialchars($description);
				}

				//Choix de la question : la première du sondage, celle passée dans l'url par défaut.
				$questions = $sondage->Questions;
				if (!empty($_GET['id2']) && is_numeric($_GET['id2']))
				{
					$question = Doctrine_Core::getTable('SondageQuestion')->find($_GET['id2']);
					if ($question['sondage_id'] != $sondage['id'])
						return redirect(13, 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html', MSG_ERROR);
				}
				else
				{
					$question = $questions[0];
				}
				$a_vote = $question->aVote($_SESSION['id'], $this->get('request')->getClientIp(true));

				//Récupération des textes libres si nécessaire.
				if ($question['libre'] && (!$sondage->estOuvert() || $a_vote || !verifier('sondages_voter')) && (verifier('sondages_voir_resultats') || $question['resultats_publics']))
				{
					$votes = $question->getVotesLibres();
				}

				//Détermination de l'ordre de la question.
				foreach ($questions as $i => $quest)
				{
					if ($quest['id'] == $question['id'])
					{
						$index = $i;
					}
				}

				fil_ariane(array(
					htmlspecialchars($sondage['nom']) => 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html',
					'Détails du sondage',
				));
				
				return render_to_response(array(
					'sondage'   => $sondage,
					'questions' => $questions,
					'question'  => $question,
					'reponses'  => $question->Reponses,
					'a_vote'    => $a_vote,
					'votes'     => isset($votes) ? $votes : null,
					'index'     => isset($index) ? $index : 0,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}