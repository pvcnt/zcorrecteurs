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
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contrôleur gérant l'affichage de la page recueillant une candidature (envoi de
 * la lettre de motivation, puis correction du test).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>, Vanger
 */
class PostulerAction extends Controller
{
	public function execute()
	{
	    include(__DIR__.'/../modeles/quiz.php');
	    
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
			zCorrecteurs::VerifierFormatageUrl($InfosRecrutement['recrutement_nom'], true);

			$InfosCandidature = InfosCandidature($_SESSION['id'], $_GET['id']);

			//Candidature en cours de rédaction
			if(!empty($InfosCandidature) && $InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION)
			{
				$texte_zform = $InfosCandidature['candidature_texte'];
				$quiz = empty($InfosRecrutement['recrutement_id_quiz']) ? null : true;
			}
			//Test
			elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST)
			{
				$texte_zform = $InfosCandidature['candidature_correction_corrige'];
			}
			else
			{
				$texte_zform = '';
			}


			//Si le recrutement est fini
			if(empty($InfosCandidature) && !$InfosRecrutement['depot_possible'])
				return redirect(1, 'recrutement-'.$_GET['id'].'-'.rewrite($InfosRecrutement['recrutement_nom']).'.html', MSG_ERROR);

			//Si on envoie sa candidature pour la première fois
			if(empty($InfosCandidature) && !empty($_POST['texte']))
			{
				AjouterCandidature($_GET['id']);
				return redirect(230, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut modifier son texte de motivation
			if($InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION && !empty($_POST['texte']))
			{
				EditerMotivationCandidature($InfosCandidature['candidature_id']);
				return redirect(165, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut répondre au questionnaire
			if($InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION &&
			   isset($_POST['submit']) && isset($quiz) &&
			   isset($_POST['rep']) && is_array($_POST['rep']) &&
			   $InfosCandidature['candidature_quiz_score'] === NULL)
			{
				$quiz = $this->get('zco_quiz.manager.quiz')->get($InfosRecrutement['recrutement_id_quiz']);
				$questions = $quiz->Questions($_POST['rep']);
				$note = $quiz->Soumettre($questions, false);

				FinQuiz($InfosCandidature);
				if (!isset($_POST['commentaires']) || !is_array($_POST['commentaires']))
					$_POST['commentaires'] = array();

				$reponses = $commentaires = array();
				foreach ($questions as $q)
				{
					if (isset($_POST['commentaires'][$q['id']]))
						$commentaires[$q['id']] = $_POST['commentaires'][$q['id']];
					if (isset($_POST['rep'.$q['id']]))
						$reponses[$q['id']] = $_POST['rep'.$q['id']];
				}
				EnregistrerReponses($InfosRecrutement['recrutement_id'], $reponses, $commentaires);

				EditerQuestionnaireCandidature($InfosCandidature['candidature_id'], $note);
				return redirect(165, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut valider l'envoi de la candidature
			if(isset($_POST['confirmer1']) && $InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION && $InfosRecrutement['depot_possible'])
			{
				EnvoyerMotivationCandidature($InfosCandidature['candidature_id']);
				return redirect(164, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut modifier sa correction
			if($InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && !empty($_POST['texte']) && $InfosCandidature['candidature_test_type'] == TEST_TEXTE)
			{
				EditerCorrectionCandidature($InfosCandidature['candidature_id']);
				return redirect(232, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut envoyer un .tuto corrigé
			if(isset($_POST['valider_tuto']) && !empty($_FILES['tuto']) && $InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && $InfosCandidature['correction_possible'] && $InfosCandidature['candidature_test_type'] == TEST_TUTO)
			{
				if(!empty($_FILES['tuto']) AND $_FILES['tuto']['size'] > 0)
				{
					if (UPLOAD_ERR_OK == $_FILES['tuto']['error'])
					{
						$extension_fichier = mb_strtolower(mb_strrchr($_FILES['tuto']['name'], '.'));

						//Vérification de l'extension.
						if($extension_fichier != '.tuto')
							return redirect(125, 'postuler-'.$_GET['id'].'.html', MSG_ERROR);

						//Déplacement du fichier temporaire vers le dossier des tutos
						move_uploaded_file($_FILES['tuto']['tmp_name'], BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['candidature_test_tuto']);
					}
					else
						throw new \RuntimeException('Erreur lors de l\'envoi du tutoriel.', 500);
				}
				$_POST['texte'] = '';
				EditerCorrectionCandidature($InfosCandidature['candidature_id']);
				EnvoyerCorrectionCandidature($InfosCandidature['candidature_id']);
				return redirect(231, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut envoyer un .tuto et un texte corrigé
			if(isset($_POST['valider_tuto']) && !empty($_FILES['tuto']) && !empty($_FILES['texte']) && $InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && $InfosCandidature['correction_possible'] && $InfosCandidature['candidature_test_type'] == TEST_DEFAUT)
			{
				if(!empty($_FILES['tuto']) AND $_FILES['tuto']['size'] > 0)
				{
					if (UPLOAD_ERR_OK == $_FILES['tuto']['error'])
					{
						$extension_fichier = mb_strtolower(mb_strrchr($_FILES['tuto']['name'], '.'));

						//Vérification de l'extension.
						if($extension_fichier != '.tuto')
							return redirect(125, 'postuler-'.$_GET['id'].'.html', MSG_ERROR);

						//Déplacement du fichier temporaire vers le dossier des tutos
						move_uploaded_file($_FILES['tuto']['tmp_name'], BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_tuto']);
					}
					else
						throw new \RuntimeException('Erreur lors de l\'envoi du tutoriel.', 500);
				}
				if(!empty($_FILES['texte']) AND $_FILES['texte']['size'] > 0)
				{
					if (UPLOAD_ERR_OK == $_FILES['texte']['error'])
					{
						$extension_fichier = mb_strtolower(mb_strrchr($_FILES['texte']['name'], '.'));

						//Vérification de l'extension.
						if($extension_fichier != '.txt')
							return redirect(125, 'postuler-'.$_GET['id'].'.html', MSG_ERROR);

						//Déplacement du fichier temporaire vers le dossier des tutos
						move_uploaded_file($_FILES['texte']['tmp_name'], BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_texte']);
					}
					else
						throw new \RuntimeException('Erreur lors de l\'envoi du tutoriel.', 500);
				}
				$_POST['texte'] = '';
				EditerCorrectionCandidature($InfosCandidature['candidature_id']);
				EnvoyerCorrectionCandidature($InfosCandidature['candidature_id']);
				return redirect(231, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on veut valider l'envoi de la correction
			if(isset($_POST['confirmer2']) && $InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && $InfosCandidature['correction_possible'])
			{
				EnvoyerCorrectionCandidature($InfosCandidature['candidature_id']);
				return redirect(231, 'postuler-'.$_GET['id'].'.html');
			}
			//Si on annule
			if(isset($_POST['annuler']))
			{
				return new RedirectResponse('postuler-'.$_GET['id'].'.html');
			}

			//Paramétrage de la vue
			fil_ariane(array(
				htmlspecialchars($InfosRecrutement['recrutement_nom']) => 'recrutement-'.$InfosRecrutement['recrutement_id'].'-'.rewrite($InfosRecrutement['recrutement_nom']).'.html',
				'Postuler'
			));
			\Page::$titre = 'Postuler - '.$InfosRecrutement['recrutement_nom'];
			
			return render_to_response(array(
				'InfosRecrutement' => $InfosRecrutement,
				'InfosCandidature' => $InfosCandidature,
				'texte_zform'      => $texte_zform,
				'quiz'             => isset($quiz) ? $quiz : null,
			));
		}
		else
			return redirect(228, '/recrutement/', MSG_ERROR);
	}
}
