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

namespace Zco\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant les actions liées aux quiz.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 *         Ziame <ziame@zcorrecteurs.fr>
 *         mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Se charge de la soumission du quiz en Ajax.
	 */
	public function ajaxJouerAction()
	{
		$quiz = \Doctrine_Core::getTable('Quiz')->find($_POST['quiz_id']);
		if ($quiz === false || !$quiz->visible)
		{
			return new Response('ERREUR');
		}

		$questions = $quiz->Questions($_POST['rep']);
		$note = $quiz->Soumettre($questions);
		$note = 'Vous avez obtenu <strong>'.$note.'/20</strong>.';

		$ret = array('note' => $note, 'reponses' => array());

		foreach ($questions as $question)
		{
			if ($_POST['rep'.$question['id']] == $question['reponse_juste'] && $_POST['rep'.$question['id']] != 0)
			{
				$tmp = '<div class="correction juste"><span class="type">Bonne réponse</span><br />';
			}
			else
			{
				$tmp = '<div class="correction faux"><span class="type">Mauvaise réponse</span><br />'.
				'La bonne réponse était : <em>'.$this->get('zco_parser.parser')->parse($question['reponse'.$question['reponse_juste']]).'</em></p>';
			}

			if(!empty($question['explication']))
			{
				$tmp .= '<p>Cette question dispose d\'une explication lui étant associée.'.
					//' - <a href="#" onclick="$(\'explication_'.$question['id'].'\').slide(); return false;" class="gras">'.
					//'Afficher</a>'.
					'</p>'.
					'<div id="explication_'.$question['id'].'" class="explication">'.
					$this->get('zco_parser.parser')->parse($question['explication']).'</div>';
			}

			$ret['reponses'][$question['id']] = $tmp.'</div>';
			$ret['achoisi'][$question['id']] = $_POST['rep'.$question['id']];
			$ret['enfait'][$question['id']] = $question['reponse_juste'];
		}

		return new Response(json_encode($ret));
	}

	/**
	 * Affiche la liste des quiz disponibles.
	 */
	public function indexAction()
	{
		$registry = $this->get('zco_core.registry');
		\zCorrecteurs::VerifierFormatageUrl();
		fil_ariane('Accueil du quiz');
		
		return render_to_response(array(
			'ListerQuiz'   => \Doctrine_Core::getTable('Quiz')->Lister(),
			'bloc_accueil' => $registry->get('bloc_accueil'),
			'QuizSemaine'  => $registry->get('accueil_quiz', null),
		));
	}

	/**
	 * Affiche les questions d'un quiz et la correction une fois
	 * celui-ci soumis.
	 */
	public function quizAction()
	{
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
    		return redirect(10, '/quiz/', MSG_ERROR);
		}
		
		$quiz = \Doctrine_Core::getTable('Quiz')->find($_GET['id']);
		if($quiz === false || !verifier('quiz_voir', $quiz['categorie_id'])
		|| !$quiz->visible)
			return redirect(10, 'index.html', MSG_ERROR);

		\zCorrecteurs::VerifierFormatageUrl($quiz['nom'], true);

		\Page::$titre = htmlspecialchars($quiz['nom']);
		\Page::$description = htmlspecialchars($quiz['description']);

		//Si on a répondu au quiz
		if (isset($_POST['submit']))
		{
			\Page::$titre .= ' - Correction';
			$questions = $quiz->Questions($_POST['rep']);
			$note = $quiz->Soumettre($questions);

			//Inclusion de la vue
			fil_ariane($quiz['categorie_id'], array(
				htmlspecialchars($quiz['nom']) => 'quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html',
				'Correction du quiz'
			));
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoCoreBundle/Resources/public/css/zcode.css',
			    '@ZcoCoreBundle/Resources/public/css/zform.css',
			));
			
			return render_to_response('ZcoQuizBundle::correction.html.php', array(
				'InfosQuiz' => $quiz,
				'ListeQuestions' => $questions,
				'Note' => $note,
			));
		}

		//Sinon on veut juste le voir
		fil_ariane($quiz['categorie_id'], array(
				htmlspecialchars($quiz['nom']) => 'quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html',
				'Répondre au quiz'
		));
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoCoreBundle/Resources/public/css/zform.css',
		    '@ZcoQuizBundle/Resources/public/css/quiz.css',
		    '@ZcoQuizBundle/Resources/public/js/quiz.js',
		));
	    
		return render_to_response('ZcoQuizBundle::jouer.html.php', array(
			'InfosQuiz' => $quiz,
			'ListeQuestions' => $quiz->Questions(),
		));
    }

	/**
	 * Affiche les statistiques individuelles d'un membre.
	 */
	public function mesStatistiquesAction()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		\Page::$titre = 'Mes statistiques d\'utilisation du quiz';

		//Si on veut accéder à d'autres statistiques que les siennes
		if (
		    (empty($_GET['id']) || !is_numeric($_GET['id']))
		    || ($_GET['id'] != $_SESSION['id'] && !verifier('quiz_stats'))
		)
		{
			$_GET['id'] = $_SESSION['id'];
		}

		$InfosUtilisateur = InfosUtilisateur($_GET['id']);
		if ($_GET['id'] != $_SESSION['id'])
		{
			\zCorrecteurs::VerifierFormatageUrl($InfosUtilisateur['utilisateur_pseudo'], true);
		}
		else
		{
			\zCorrecteurs::VerifierFormatageUrl(null, true);
		}

		$_SESSION['graphe_quiz'] = \Doctrine_Core::getTable('QuizScore')->StatistiquesMembreGraphe($_GET['id']);
		$_SESSION['graphe_quiz_type'] = 1;

		// Inclusion de la vue
		fil_ariane('Mes statistiques d\'utilisation du quiz');
		
		return render_to_response(array(
			'InfosUtilisateur' => $InfosUtilisateur,
			'Statistiques' => \Doctrine_Core::getTable('QuizScore')->StatistiquesMembre($_GET['id']),
		));
	}

	/**
	 * Affiche la page de gestion des quiz.
	 */
	public function gestionAction()
	{
		if (!verifier_array([['quiz_ajouter', 'quiz_editer', 'quiz_editer_siens', 'quiz_supprimer', 'quiz_supprimer_siens',
            'quiz_ajouter_questions', 'quiz_ajouter_questions_siens', 'quiz_editer_ses_questions', 'quiz_editer_questions',
            'quiz_supprimer_questions', 'quiz_supprimer_ses_questions']])) {
		    throw new AccessDeniedHttpException();
        }
		\Page::$titre = 'Gérer les quiz';
		fil_ariane('Gestion des quiz');
		
		return render_to_response(array(
		    'ListerQuiz' => \Doctrine_Core::getTable('Quiz')->Lister(true),
		));
	}

	/**
	 * Ajoute une question à un quiz.
	 */
	public function ajouterQuestionAction()
	{
		\Page::$titre = 'Ajouter une question au quiz';

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
		    return redirect(10, 'gestion.html', MSG_ERROR);
	    }
	    
		$quiz = \Doctrine_Core::getTable('Quiz')->find($_GET['id']);
		if ($quiz === false)
		{
			return redirect(10, 'gestion.html', MSG_ERROR);
		}
		\zCorrecteurs::VerifierFormatageUrl($quiz['nom'], true);

		if (verifier('quiz_ajouter_questions') || ($quiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_ajouter_questions_siens')))
		{
			//Si on veut ajouter une question
			if (!empty($_POST['question']) && !empty($_POST['rep1']) && !empty($_POST['rep2']) && !empty($_POST['rep_juste']) && is_numeric($_POST['rep_juste']))
			{
				if (($_POST['rep_juste'] == 3 && empty($_POST['rep3'])) || ($_POST['rep_juste'] == 4 && empty($_POST['rep4'])))
				{
					return redirect(8, 'ajouter-question-'.$_GET['id'].'.html', MSG_ERROR);
				}
				else
				{
					$question = new \QuizQuestion();
					$question['quiz_id']       = $quiz['id'];
					$question['question']      = $_POST['question'];
					$question['reponse1']      = $_POST['rep1'];
					$question['reponse2']      = $_POST['rep2'];
					$question['reponse3']      = $_POST['rep3'];
					$question['reponse4']      = $_POST['rep4'];
					$question['reponse_juste'] = $_POST['rep_juste'];
					$question['explication']   = $_POST['texte'];
					$question->save();

					return redirect(1, 'editer-quiz-'.$_GET['id'].'.html');
				}
			}

			//Inclusion de la vue
			fil_ariane($quiz['categorie_id'], array(
				htmlspecialchars($quiz['nom']) => 'quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html',
				'Ajouter une question au quiz'
			));
			
			return render_to_response(array('InfosQuiz' => $quiz));
		}
		else
		{
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * Ajoute un nouveau quiz.
	 */
	public function ajouterQuizAction()
	{
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Ajouter un quiz';

		//Si on veut ajouter un quiz
		if (!empty($_POST['nom']) && is_numeric($_POST['categorie']) && is_numeric($_POST['difficulte']))
		{
			$quiz = new \Quiz();
			$quiz['nom']          = $_POST['nom'];
			$quiz['categorie_id'] = $_POST['categorie'];
			$quiz['description']  = $_POST['description'];
			$quiz['difficulte']   = $_POST['difficulte'];
			$quiz['aleatoire']    = intval($_POST['aleatoire']);
			$quiz->save();

			return redirect(4, 'editer-quiz-'.$quiz['id'].'.html');
		}
		
		$difficulte = \Doctrine_Core::getTable('Quiz')->getDefinitionOf('difficulte');
		fil_ariane('Ajouter un quiz');
		
		return render_to_response(array(
			'ListerCategories' => ListerEnfants(InfosCategorie(GetIDCategorieCourante())),
			'Difficultes' => $difficulte['values'],
		));
	}

	/**
	 * Modifie une question d'un quiz.
	 */
	public function editerQuestionAction()
	{
		\Page::$titre = 'Modifier une question du quiz';

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
		    return redirect(7, 'gestion.html', MSG_ERROR);
	    }
	    
		$question = \Doctrine_Core::getTable('QuizQuestion')->find($_GET['id']);
		if ($question === false)
		{
			return redirect(322, '/quiz/', MSG_ERROR);
		}
		\zCorrecteurs::VerifierFormatageUrl($question->Quiz['nom'], true);

		if (verifier('quiz_editer_questions') || ($question['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_ses_questions')))
		{
			//Si on veut éditer une question
			if (!empty($_POST['question']) && !empty($_POST['rep1']) && !empty($_POST['rep2']) && !empty($_POST['rep_juste']) && is_numeric($_POST['rep_juste']))
			{
				$question['question']      = $_POST['question'];
				$question['reponse1']      = $_POST['rep1'];
				$question['reponse2']      = $_POST['rep2'];
				$question['reponse3']      = $_POST['rep3'];
				$question['reponse4']      = $_POST['rep4'];
				$question['reponse_juste'] = $_POST['rep_juste'];
				$question['explication']   = $_POST['texte'];
				$question->save();

				return redirect(2, 'editer-quiz-'.$question->Quiz['id'].'.html');
			}
			
			//Inclusion de la vue
			fil_ariane($question->Quiz['categorie_id'], array(
				htmlspecialchars($question->Quiz['nom']) => 'quiz-'.$question['quiz_id'].'-'.rewrite($question->Quiz['nom']).'.html',
				'Modifier une question du quiz'
			));
			
			return render_to_response(array('InfosQuestion' => $question));
		}
		else
		{
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * Modifie les propriétés d'un quiz.
	 */
	public function editerQuizAction()
	{
		\Page::$titre = 'Modifier un quiz';

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
		    return redirect(10, 'gestion.html', MSG_ERROR);
	    }
	    
		$quiz = \Doctrine_Core::getTable('Quiz')->find($_GET['id']);
		if ($quiz === false)
		{
			return redirect(10, 'gestion.html', MSG_ERROR);
		}
		\zCorrecteurs::VerifierFormatageUrl($quiz['nom'], true);

		if (verifier('quiz_editer') ||
			($quiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_siens')) ||
			verifier('quiz_ajouter_questions') ||
			($quiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_ajouter_questions_siens')) ||
			verifier('quiz_editer_questions') || verifier('quiz_supprimer_questions') ||
			verifier('quiz_editer_ses_questions') || verifier('quiz_supprimer_ses_questions')
		)
		{
			//Si on veut éditer un quiz
			if ((verifier('quiz_editer') ||
				($quiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_siens'))) &&
				!empty($_POST['nom']) && is_numeric($_POST['categorie']) && is_numeric($_POST['difficulte'])
			)
			{
				$quiz['nom']          = $_POST['nom'];
				$quiz['categorie_id'] = $_POST['categorie'];
				$quiz['description']  = $_POST['description'];
				$quiz['difficulte']   = $_POST['difficulte'];
				$quiz['aleatoire']    = intval($_POST['aleatoire']);
				$quiz->save();

				return redirect(5, 'editer-quiz-'.$_GET['id'].'-'.rewrite($_POST['nom']).'.html');
			}
			$difficulte = \Doctrine_Core::getTable('Quiz')->getDefinitionOf('difficulte');

			//Inclusion de la vue
			fil_ariane($quiz['categorie_id'], array(
				htmlspecialchars($quiz['nom']) => 'quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html',
				'Modifier le quiz'
			));
            
			return render_to_response(array(
				'InfosQuiz' => $quiz,
				'ListeQuestions' => $quiz->Questions(false),
				'ListerCategories' => ListerEnfants(InfosCategorie(GetIDCategorieCourante())),
				'Difficultes' => $difficulte['values'],
			));
		}
		else
			throw new AccessDeniedHttpException();
	}

	/**
	 * Supprime une question d'un quiz.
	 */
	public function supprimerQuestionAction()
	{
		\Page::$titre = 'Supprimer une question';

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
		    return redirect(121, 'gestion.html', MSG_ERROR);
	    }
	    
		$question = \Doctrine_Core::getTable('QuizQuestion')->find($_GET['id']);
		if ($question === false)
		{
			return redirect(7, '/quiz/', MSG_ERROR);
		}
		\zCorrecteurs::VerifierFormatageUrl($question->Quiz['nom'], true);

		if (verifier('quiz_supprimer_questions') || ($question['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_supprimer_ses_questions')))
		{
			//Si on veut supprimer une question
			if (isset($_POST['confirmer']))
			{
				$question->delete();
				return redirect(3, 'editer-quiz-'.$question['quiz_id'].'.html');
			}
			//Si on annule
			elseif (isset($_POST['annuler']))
			{
				return new RedirectResponse('editer-quiz-'.$question['quiz_id'].'.html');
			}

			//Inclusion de la vue
			fil_ariane($question->Quiz['categorie_id'], array(
				htmlspecialchars($question->Quiz['nom']) => 'quiz-'.$question['quiz_id'].'-'.rewrite($question->Quiz['nom']).'.html',
				'Supprimer une question du quiz'
			));
			
			return render_to_response(array('InfosQuestion' => $question));
		}
		else
		{
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * Supprime un quiz.
	 */
	public function supprimerQuizAction()
	{
		\Page::$titre = 'Supprimer un quiz';

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
		    return redirect(10, 'gestion.html', MSG_ERROR);
	    }
	    
		$quiz = \Doctrine_Core::getTable('Quiz')->find($_GET['id']);
		if ($quiz === false)
		{
			return redirect(10, 'gestion.html', MSG_ERROR);
		}
		\zCorrecteurs::VerifierFormatageUrl($quiz['nom'], true);

		if (verifier('quiz_supprimer') || ($quiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_supprimer_siens')))
		{
			//Si on veut supprimer un quiz
			if(isset($_POST['confirmer']))
			{
				$quiz->delete();
				return redirect(6, 'gestion.html');
			}
			//Si on annule
			elseif (isset($_POST['annuler']))
			{
				return new RedirectResponse('gestion.html');
			}

			//Inclusion de la vue
			fil_ariane($quiz['categorie_id'], array(
				htmlspecialchars($quiz['nom']) => 'quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html',
				'Supprimer le quiz'
			));
			
			return render_to_response(array('InfosQuiz' => $quiz));
		}
		else
		{
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * Génère le graphique de statistiques.
	 *
	 * @author Ziame <ziame@zcorrecteurs.fr>
	 */
	public function graphiqueStatsAction()
	{
		include_once(BASEPATH.'/lib/Artichow/BarPlot.class.php');
		include_once(BASEPATH.'/lib/Artichow/Graph.class.php');

		if (!isset($_SESSION['graphe_quiz']) || !isset($_SESSION['graphe_quiz_type']))
		{
		    throw new AccessDeniedHttpException();
	    }
	    
		$liste = $_SESSION['graphe_quiz'];
		$data = array();
		if ($_SESSION['graphe_quiz_type'] === 1)
		{
		    $nomOrdonnee = 'Nombre d\'obtentions';
		}
		else
		{
		    $nomOrdonnee = 'Effectifs';
		}
		
		$compteur = 0;
		foreach ($liste AS $element)
		{
			$data[$compteur] = $element;
			$compteur++;
		}
		
		// On crée le graphique
		$graph = new \Graph(800, 450);

		//On fait la mise en page
		$hautGraph = new \Color(62, 207, 248, 0);
		$basGraph = new \Color(85, 214, 251, 0);
		$couleurCourbeHaut = new \Color(100, 100, 255, 0);
		$couleurCourbeBas = new \Color(150, 150, 255, 0);
		$graph->setBackgroundGradient(new \LinearGradient($hautGraph, $basGraph, 0));

		//Légende
		$groupe = new \PlotGroup();
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new \Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set($nomOrdonnee);

		$groupe->axis->bottom->title->setFont(new \Tuffy(10));
		$groupe->axis->bottom->title->set('Notes');

		//On trace la courbe avec les données et on la configure
		$plot = new \BarPlot($data);
		$plot->setBarGradient(new \LinearGradient($couleurCourbeHaut, $couleurCourbeBas, 0));
		$plot->setXAxis(\Plot::BOTTOM);
		$plot->setYAxis(\Plot::LEFT);

		//Enfin, on mixe le tout
		$groupe->add($plot);
		$graph->add($groupe);

		// On affiche le graphique à l'écran
		$r = new Response($graph->draw(\Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		return $r;
	}

    /**
     * Valide ou dévalide un quiz.
     */
	public function validerQuizAction()
	{
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
		\Page::$titre = 'Modifier l\'état d\'un quiz';
		if (empty($_GET['id']))
		{
			return redirect(10, 'gestion.html', MSG_ERROR);
		}

		$quiz = \Doctrine_Core::getTable('Quiz')->find($_GET['id']);
		if ($quiz === false)
		{
			return redirect(10, 'gestion.html', MSG_ERROR);
		}

		$quiz->visible = $_GET['id2'] ? 1 : 0;
		$quiz->save();

		return redirect($_GET['id2'] ? 11 : 12, 'gestion.html');
	}
	
	/**
	 * Déplacer une question d'un quiz à un autre.
	 *
	 * @author mwsaz <mwsaz@zcorrecteurs.fr>
	 */
	public function deplacerQuestionAction()
	{
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(7, 'gestion.html', MSG_ERROR);
		}

        if (!verifier_array(['quiz_supprimer_questions', 'quiz_ajouter_questions'])) {
            throw new AccessDeniedHttpException();
        }

		$question = \Doctrine_Core::getTable('QuizQuestion')->find($_GET['id']);
		if ($question === false)
		{
			return redirect(7, '/quiz/', MSG_ERROR);
		}

		$ancienQuiz = $question->Quiz;
		\zCorrecteurs::VerifierFormatageUrl($ancienQuiz->nom, true);
		
		if (!empty($_POST['quiz']))
		{
			$nouveauQuiz = \Doctrine_Core::getTable('Quiz')->find($_POST['quiz']);
			if ($nouveauQuiz->id != $ancienQuiz->id)
			{
				$question->quiz_id = $nouveauQuiz->id;
				$question->save();
			}
			return redirect(13, 'editer-quiz-'.$ancienQuiz->id.'-'
				.rewrite($ancienQuiz->nom).'.html', MSG_OK);
		}

		\Page::$titre = 'Déplacer une question';
		
		return render_to_response(array(
			'question'   => $question,
			'listeQuiz'  => \Doctrine_Core::getTable('Quiz')->Lister(true),
			'ancienQuiz' => $ancienQuiz
		));
	}
	
	/**
     * Affiche les statistiques de popularité des quiz, c'est-à-dire tous les quiz
     * classés par nombre de validations, avec diverses informations pour juger de
     * l'intérêt apporté aux membres à chacun des quiz.
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     */
	public function statistiquesPopulariteAction()
	{
        if (!verifier('quiz_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
		\Page::$titre = 'Popularité des quiz';
		$listeQuiz = \Doctrine_Core::getTable('Quiz')->listerParPopularite();
		
		return render_to_response(compact('listeQuiz'));
	}
	
	/**
	 * Affiche des statistiques détaillées sur l'utilisation du module de quiz.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function statistiquesAction()
	{
        if (!verifier('quiz_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
		$annee   = isset($_GET['annee']) ? (int) $_GET['annee'] : (int) date('Y');
		$mois    = isset($_GET['mois']) ? (int) $_GET['mois'] : (int) date('m');
		$jour    = isset($_GET['jour']) ? (int) $_GET['jour'] : null;
		$id_quiz = !empty($_GET['id']) ? (int) $_GET['id'] : null;

		$annee_precedent = $mois === 1 ? $annee - 1 : $annee;
		$mois_precedent  = $mois === 1 ? 12 : $mois - 1;
		$annee_suivant   = $mois === 12 ? $annee + 1 : $annee;
		$mois_suivant    = $mois === 12 ? 1 : $mois + 1;

		//On vérifie que le quiz existe bel et bien.
		if (isset($id_quiz))
		{
			$quiz = \Doctrine_Core::getTable('Quiz')->find($id_quiz);
			if ($quiz === false)
			{
				return redirect(10, '', MSG_ERROR);
			}
		}

		//Récupération des statistiques pour afficher dans le tableau.
		if ($jour !== null)
		{
			$donnees = isset($quiz) ?
				\Doctrine_Core::getTable('QuizScore')->getStatistiquesQuizJour($id_quiz, $jour, $mois, $annee) :
				\Doctrine_Core::getTable('QuizScore')->getStatistiquesJour($jour, $mois, $annee);
		}
		else
		{
			$donnees = isset($quiz) ? 
				\Doctrine_Core::getTable('QuizScore')->getStatistiquesQuizMois($id_quiz, $mois, $annee) :
				\Doctrine_Core::getTable('QuizScore')->getStatistiquesMois($mois, $annee);
		}

		//Statistiques globales (depuis la création des quiz).
		if (!isset($jour))
		{
			$validationsTotales   = \Doctrine_Core::getTable('QuizScore')->compterTotal(isset($quiz) ? $quiz['id'] : null);
			$validationsMembres   = \Doctrine_Core::getTable('QuizScore')->compterParMembres(isset($quiz) ? $quiz['id'] : null);
			$validationsVisiteurs = \Doctrine_Core::getTable('QuizScore')->compterParVisiteurs(isset($quiz) ? $quiz['id'] : null);
			$noteMoyenne          = \Doctrine_Core::getTable('QuizScore')->noteMoyenne(isset($quiz) ? $quiz['id'] : null);
		}

		$listeQuiz = \Doctrine_Core::getTable('Quiz')->lister();
		\Page::$titre = 'Statistiques d\'utilisation des quiz';
		
		return render_to_response(compact(
			'annee', 'mois', 'jour', 'donnees', 'quiz', 'annee_suivant',
			'annee_precedent', 'mois_suivant', 'mois_precedent', 'listeQuiz',
			'validationsTotales', 'validationsMembres', 'validationsVisiteurs',
			'noteMoyenne'
		));
	}
	
	/**
     * Affiche un graphique de statistiques d'utilisation de tous les quiz confondus,
     * ou bien d'un quiz particulier sur n'importe quelle période (soit depuis la
     * création, sur une année, sur un mois ou bien sur une journée).
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     */
	public function graphiqueStatistiquesAction()
	{
		if (!verifier('quiz_stats_generales'))
		{
			throw new AccessDeniedHttpException();
		}

		$listeMois = array('Janvier', 'Février', 'Mars',
			'Avril', 'Mai', 'Juin', 'Juillet', 'Août',
			'Septembre', 'Octobre', 'Novembre', 'Décembre');

		$annee   = isset($_GET['annee']) ? (int) $_GET['annee'] : null;
		$mois    = isset($_GET['mois']) ? (int) $_GET['mois'] : null;
		$jour    = isset($_GET['jour']) ? (int) $_GET['jour'] : null;
		$id_quiz = isset($_GET['quiz']) ? (int) $_GET['quiz'] : null;

		// On vérifie que le quiz existe bel et bien.
		if (isset($id_quiz))
		{
			$quiz = \Doctrine_Core::getTable('Quiz')->find($id_quiz);
			if ($quiz === false)
			{
				throw new NotFoundHttpException('Le quiz demandé n\'existe pas.');
			}
		}

		// Évolution des validations en fonction de l'heure du jour.
		if (isset($jour, $mois, $annee))
		{
				$donnees = isset($quiz) ?
					\Doctrine_Core::getTable('QuizScore')->getGraphiqueQuizJour($id_quiz, $jour, $mois, $annee) :
					\Doctrine_Core::getTable('QuizScore')->getGraphiqueJour($jour, $mois, $annee);
				$legende = 'Heures du '.$jour.' '.lcfirst($listeMois[$mois-1]).' '.$annee;
		}
		elseif (isset($mois, $annee))
		{
			$donnees = isset($quiz) ?
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueQuizMois($id_quiz, $mois, $annee) :
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueMois($mois, $annee);
			$legende = 'Jours du mois de '.lcfirst($listeMois[$mois-1]).' '.$annee;
			$labels = array_keys($donnees['validations_totales']);
		}
		elseif (isset($annee))
		{
			$donnees = isset($quiz) ?
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueQuizAnnee($id_quiz, $annee) :
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueAnnee($annee);
			$legende = 'Mois de l\'année '.$annee;
			$labels = $listeMois;
			if ($annee === (int)date('Y'))
			{
				$labels = array_slice($labels, 0, (int) date('n'));
			}
		}
		else
		{
			$donnees = isset($quiz) ?
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueQuizGlobal($quiz['id'], $quiz['date']) :
				\Doctrine_Core::getTable('QuizScore')->getGraphiqueGlobal();

			$labels = array();
			foreach ($donnees['validations_totales'] as $cle => $valeur)
			{
				list($annee, $mois) = explode('-', $cle);
				$labels[] = $listeMois[(int)$mois].' '.$annee;
			}
			unset($annee, $mois);
		}

		include(BASEPATH.'/lib/Artichow/Graph.class.php');
		include(BASEPATH.'/lib/Artichow/LinePlot.class.php');

		$graph = new \Graph(700, 400);
		$graph->setAntiAliasing(true);
		$hautGraph = new \Color(62, 207, 248, 0);
		$basGraph = new \Color(85, 214, 251, 0);
		$graph->setBackgroundGradient(new \LinearGradient($hautGraph, $basGraph, 0));

		// Légende
		$groupe = new \PlotGroup();
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new \Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set(isset($quiz) ? 'Validations du quiz '.htmlspecialchars($quiz['nom']) : 'Validations des quiz');

		if (!isset($annee))
		{
			$groupe->axis->bottom->setLabelInterval(6);
		}

		$groupe->axis->bottom->title->setFont(new \Tuffy(10));
		if (isset($legende))
		{
			$groupe->axis->bottom->title->set($legende);
		}
		if (isset($labels))
		{
			$groupe->axis->bottom->setLabelText($labels);
		}

		$plot = new \LinePlot(array_values($donnees['validations_totales']));
		$plot->setColor(new \Blue());
		$plot->setXAxis(\Plot::BOTTOM);
		$plot->setYAxis(\Plot::LEFT);
		$groupe->add($plot);
		$groupe->legend->add($plot, 'Validations totales');

		$plot = new \LinePlot(array_values($donnees['validations_visiteurs']));
		$plot->setColor(new \Red());
		$plot->setXAxis(\Plot::BOTTOM);
		$plot->setYAxis(\Plot::LEFT);
		$groupe->add($plot);
		$groupe->legend->add($plot, 'Validations par des visiteurs');

		$graph->add($groupe);

		// On affiche le graphique à l'écran
		$r = new Response($graph->draw(\Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		return $r;
	}
	
	/**
     * Affiche un graphique de répartition des notes de tous les quiz confondus,
     * ou bien d'un quiz particulier sur toute la période de validité des quiz.
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     */
	public function graphiqueNotesAction()
	{
		if (!verifier('quiz_stats_generales'))
		{
			throw new AccessDeniedHttpException();
		}
		$id_quiz = isset($_GET['quiz']) ? (int) $_GET['quiz'] : null;

		//On vérifie que le quiz existe bel et bien.
		if (isset($id_quiz))
		{
			$quiz = \Doctrine_Core::getTable('Quiz')->find($id_quiz);
			if ($quiz === false)
			{
				throw new NotFoundHttpException('Le quiz demandé n\'existe pas.');
			}
		}

		$donnees = isset($quiz) ?
			\Doctrine_Core::getTable('QuizScore')->getGraphiqueQuizNotes($quiz['id']) :
			\Doctrine_Core::getTable('QuizScore')->getGraphiqueNotes();

		include(BASEPATH.'/lib/Artichow/Graph.class.php');
		include(BASEPATH.'/lib/Artichow/BarPlot.class.php');

		$graph = new \Graph(700, 400);
		$hautGraph = new \Color(62, 207, 248, 0);
		$basGraph = new \Color(85, 214, 251, 0);
		$couleurCourbeHaut = new \Color(100, 100, 255, 0);
		$couleurCourbeBas = new \Color(150, 150, 255, 0);
		$graph->setBackgroundGradient(new \LinearGradient($hautGraph, $basGraph, 0));

		$groupe = new \PlotGroup();
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new \Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set('Répartition des notes');

		$groupe->axis->bottom->title->setFont(new \Tuffy(10));
		$groupe->axis->bottom->title->set('Notes obtenues'.(isset($quiz) ? ' sur le quiz '.htmlspecialchars($quiz['nom']) : ''));

		$plot = new \BarPlot($donnees);
		$plot->setBarGradient(new \LinearGradient($couleurCourbeHaut, $couleurCourbeBas, 0));
		$plot->setXAxis(\Plot::BOTTOM);
		$plot->setYAxis(\Plot::LEFT);
		$groupe->add($plot);

		$graph->add($groupe);

		// On affiche le graphique à l'écran
		$r = new Response($graph->draw(\Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		return $r;
	}
}
