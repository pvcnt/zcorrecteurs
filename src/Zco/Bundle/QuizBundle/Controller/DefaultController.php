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

namespace Zco\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\QuizBundle\Chart\MyStatsChart;

/**
 * Contrôleur gérant les actions liées aux quiz.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author Ziame <ziame@zcorrecteurs.fr>
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Affiche la liste des quiz disponibles.
     */
    public function indexAction()
    {
        fil_ariane('Accueil du quiz');
        $registry = $this->get('zco_core.registry');
        $pinnedQuiz = $registry->get('bloc_accueil') === 'quiz' ? $registry->get('accueil_quiz', null) : null;
        $quizList = $this->get('zco_quiz.manager.quiz')->lister();

        return render_to_response('ZcoQuizBundle:Default:index.html.php', [
            'quizList' => $quizList,
            'pinnedQuiz' => $pinnedQuiz,
        ]);
    }

    /**
     * Affiche les questions d'un quiz .
     */
    public function showAction($id, $slug)
    {
        $repository = $this->get('zco_quiz.manager.quiz');
        $quiz = $repository->get($id);
        if ($quiz === false || !$quiz->visible) {
            throw new NotFoundHttpException();
        }
        $questions = $repository->findQuestions($quiz['id'], $quiz['aleatoire']);
        //\zCorrecteurs::VerifierFormatageUrl($quiz['nom'], true);

        \Page::$titre = htmlspecialchars($quiz['nom']);
        \Page::$description = htmlspecialchars($quiz['description']);
        fil_ariane($quiz['categorie_id'], array(htmlspecialchars($quiz['nom'])));
        $this->get('zco_vitesse.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoCoreBundle/Resources/public/css/zform.css',
            '@ZcoQuizBundle/Resources/public/css/quiz.css',
            '@ZcoQuizBundle/Resources/public/js/quiz.js',
        ]);

        return render_to_response('ZcoQuizBundle:Default:show.html.php', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    /**
     * Affiche les statistiques individuelles d'un membre.
     */
    public function myStatsAction()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        $repository = $this->get('zco_quiz.manager.score');
        $avgNote = $repository->getAverage($_SESSION['id']);
        $nbNotes = $repository->count($_SESSION['id']);
        $lastNotes = $repository->find($_SESSION['id'], 30);
        \Page::$titre = 'Mes statistiques d\'utilisation du quiz';

        return render_to_response('ZcoQuizBundle:Default:myStats.html.php', [
            'avgNote' => $avgNote,
            'nbNotes' => $nbNotes,
            'lastNotes' => $lastNotes,
        ]);
    }

    /**
     * Génère le graphique de statistiques.
     *
     * @author Ziame <ziame@zcorrecteurs.fr>
     */
    public function myStatsChartAction()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        $distribution = $this->get('zco_quiz.manager.score')->getDistribution($_SESSION['id']);

        return (new MyStatsChart($distribution))->getResponse();
    }
}