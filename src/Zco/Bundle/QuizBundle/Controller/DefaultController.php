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

namespace Zco\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;
use Zco\Bundle\QuizBundle\Chart\GlobalStatsChart;
use Zco\Bundle\QuizBundle\Chart\MyStatsChart;
use Zco\Bundle\QuizBundle\Entity\QuizScoreManager;

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
        \Page::$titre = 'Quiz';
        $registry = $this->get('zco_core.registry');
        $pinnedQuiz = $registry->get('bloc_accueil') === 'quiz' ? $registry->get('accueil_quiz', null) : null;
        $quizList = $this->get('zco_quiz.manager.quiz')->lister();

        return render_to_response('ZcoQuizBundle::index.html.php', [
            'quizList' => $quizList,
            'pinnedQuiz' => $pinnedQuiz,
        ]);
    }

    /**
     * Affiche les questions d'un quiz.
     *
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function showAction($id, $slug, Request $request)
    {
        $repository = $this->get('zco_quiz.manager.quiz');
        $quiz = $repository->get($id);
        if ($quiz === false || !$quiz->visible) {
            throw new NotFoundHttpException();
        }
        //TODO: check slug.

        if ($request->getMethod() === 'POST') {
            $questions = $repository->findQuestions($quiz['id'], $_POST['rep']);
            $note = $quiz->Soumettre($questions);
            $reponses = [];
            foreach ($questions as $question) {
                $choice = $_POST['rep' . $question['id']];
                $correct = $choice != 0 && $choice == $question['reponse_juste'];
                $reponses[] = [
                    'choice' => $choice,
                    'correct' => $correct,
                ];
            }

            \Page::$titre = htmlspecialchars($quiz['nom']) . ' - Correction';
            fil_ariane([
                'Quiz' => $this->generateUrl('zco_quiz_index'),
                htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]),
                'Correction',
            ]);

            return render_to_response('ZcoQuizBundle::correction.html.php', [
                'quiz' => $quiz,
                'questions' => $questions,
                'note' => $note,
                'reponses' => $reponses,
            ]);
        }

        \Page::$titre = htmlspecialchars($quiz['nom']);
        \Page::$description = htmlspecialchars($quiz['description']);
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($quiz['nom']),
        ]);

        $questions = $repository->findQuestions($quiz['id'], $quiz['aleatoire']);

        return render_to_response('ZcoQuizBundle::show.html.php', [
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
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Mes statistiques',
        ]);

        return render_to_response('ZcoQuizBundle::myStats.html.php', [
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

    /**
     * Display a list of all quiz, including unpublished ones.
     *
     * @return Response
     */
    public function adminAction()
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Gestion des quiz';
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Gestion des quiz',
        ]);

        $quizList = $this->get('zco_quiz.manager.quiz')->lister(true);

        return render_to_response('ZcoQuizBundle::admin.html.php', [
            'quizList' => $quizList,
        ]);
    }

    /**
     * Add a new question to an existing quiz.
     *
     * @param int $quizId Quiz identifier.
     * @return Response
     */
    public function newQuestionAction($quizId)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $quiz = $this->get('zco_quiz.manager.quiz')->get($quizId);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }

        if (!empty($_POST['question']) && !empty($_POST['rep1']) && !empty($_POST['rep2']) && !empty($_POST['rep_juste']) && is_numeric($_POST['rep_juste'])) {
            if (($_POST['rep_juste'] == 3 && empty($_POST['rep3'])) || ($_POST['rep_juste'] == 4 && empty($_POST['rep4']))) {
                return redirect(
                    'Vous avez indiqué une réponse comme juste alors que son contenu est vide !',
                    $this->generateUrl('zco_quiz_newQuestion', ['quizId' => $quiz['id']]),
                    MSG_ERROR
                );
            }
            $question = new \QuizQuestion();
            $question['quiz_id'] = $quiz['id'];
            $question['utilisateur_id'] = $_SESSION['id'];
            $question['date'] = date('Y-m-d H:i:s');
            $question['question'] = $_POST['question'];
            $question['reponse1'] = $_POST['rep1'];
            $question['reponse2'] = $_POST['rep2'];
            $question['reponse3'] = $_POST['rep3'];
            $question['reponse4'] = $_POST['rep4'];
            $question['reponse_juste'] = $_POST['rep_juste'];
            $question['explication'] = $_POST['texte'];
            $question->save();

            $this->get('cache')->delete('quiz_liste_nouveaux');

            return redirect(
                'La question a bien été ajoutée.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $quiz['id']])
            );
        }

        \Page::$titre = 'Ajouter une question';
        fil_ariane(array(
            htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_editQuiz', ['id' => $quiz['id']]),
            'Ajouter une question'
        ));

        return render_to_response('ZcoQuizBundle::newQuestion.html.php', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Ajoute un nouveau quiz.
     *
     * @return Response
     */
    public function newQuizAction()
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }

        if (!empty($_POST['nom']) && is_numeric($_POST['categorie']) && is_numeric($_POST['difficulte'])) {
            $quiz = new \Quiz();
            $quiz['utilisateur_id'] = $_SESSION['id'];
            $quiz['date'] = date('Y-m-d H:i:s');
            $quiz['nom'] = $_POST['nom'];
            $quiz['categorie_id'] = $_POST['categorie'];
            $quiz['description'] = $_POST['description'];
            $quiz['difficulte'] = $_POST['difficulte'];
            $quiz['aleatoire'] = intval($_POST['aleatoire']);
            $quiz->save();

            return redirect(
                'Le quiz a bien été ajouté.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $quiz['id']])
            );
        }

        $categories = CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorie('quiz'));
        \Page::$titre = 'Ajouter un quiz';

        return render_to_response('ZcoQuizBundle::newQuiz.html.php', [
            'categories' => $categories,
            'levels' => \Quiz::LEVELS,
        ]);
    }

    /**
     * Edit an existing question of a quiz.
     *
     * @param int $id Question identifier.
     * @return Response
     */
    public function editQuestionAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get('zco_quiz.manager.quiz')->getQuestion($id);
        if (!$question) {
            throw new NotFoundHttpException();
        }

        if (!empty($_POST['question']) && !empty($_POST['rep1']) && !empty($_POST['rep2']) && !empty($_POST['rep_juste']) && is_numeric($_POST['rep_juste'])) {
            $question['question'] = $_POST['question'];
            $question['reponse1'] = $_POST['rep1'];
            $question['reponse2'] = $_POST['rep2'];
            $question['reponse3'] = $_POST['rep3'];
            $question['reponse4'] = $_POST['rep4'];
            $question['reponse_juste'] = $_POST['rep_juste'];
            $question['explication'] = $_POST['texte'];
            $question->save();

            return redirect(
                'La question a bien été modifiée.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $question->Quiz['id']])
            );
        }

        \Page::$titre = 'Modifier une question';
        fil_ariane(array(
            htmlspecialchars($question->Quiz['nom']) => $this->generateUrl('zco_quiz_editQuiz', ['id' => $question->Quiz['id']]),
            'Modifier une question'
        ));

        return render_to_response('ZcoQuizBundle::editQuestion.html.php', [
            'question' => $question,
        ]);
    }

    /**
     * Edit an existing quiz.
     *
     * @param int $id Quiz identifier.
     * @return Response
     */
    public function editQuizAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $quizManager = $this->get('zco_quiz.manager.quiz');
        $quiz = $quizManager->get($id);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }

        if (!empty($_POST['nom']) && is_numeric($_POST['categorie']) && is_numeric($_POST['difficulte'])) {
            $quiz['nom'] = $_POST['nom'];
            $quiz['categorie_id'] = $_POST['categorie'];
            $quiz['description'] = $_POST['description'];
            $quiz['difficulte'] = $_POST['difficulte'];
            $quiz['aleatoire'] = intval($_POST['aleatoire']);
            $quiz->save();

            return redirect(
                'Le quiz a bien été modfié.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $quiz['id']])
            );
        }

        $questions = $quizManager->findQuestions($quiz['id']);
        $categories = CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorie('quiz'));
        \Page::$titre = 'Modifier le quiz';
        fil_ariane(htmlspecialchars($quiz['nom']));

        return render_to_response('ZcoQuizBundle::editQuiz.html.php', [
            'quiz' => $quiz,
            'questions' => $questions,
            'categories' => $categories,
            'levels' => \Quiz::LEVELS,
        ]);
    }

    /**
     * Supprime une question d'un quiz.
     */
    public function deleteQuestionAction($id, Request $request)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get('zco_quiz.manager.quiz')->getQuestion($id);
        if (!$question) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() === 'POST') {
            $question->delete();

            return redirect(
                'La question a bien été supprimée.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $question->quiz['id']])
            );
        }

        \Page::$titre = 'Supprimer une question';
        fil_ariane($question->Quiz['categorie_id'], array(
            htmlspecialchars($question->Quiz['nom']) => $this->generateUrl('zco_quiz_editQuiz', ['id' => $question->quiz['id']]),
            'Supprimer une question'
        ));

        return render_to_response('ZcoQuizBundle::deleteQuestion.html.php', [
            'question' => $question,
        ]);
    }

    /**
     * Delete a quiz, after confirmation.
     *
     * @param int $id Quiz identifier.
     * @param Request $request HTTP request.
     * @return Response
     */
    public function deleteQuizAction($id, Request $request)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $quiz = $this->get('zco_quiz.manager.quiz')->get($id);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() === 'POST') {
            $quiz->delete();

            return redirect('Le quiz a bien été supprimé.', $this->generateUrl('zco_quiz_admin'));
        }

        \Page::$titre = 'Supprimer le quiz';
        fil_ariane($quiz['categorie_id'], array(
            htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]),
            'Supprimer le quiz'
        ));

        return render_to_response('ZcoQuizBundle::deleteQuiz.html.php', array('quiz' => $quiz));
    }

    /**
     * Valide ou dévalide un quiz.
     */
    public function publishQuizAction($id, $status, Request $request)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        if ($request->query->get('tk') !== $_SESSION['token']) {
            // CSRF potential problem.
            throw new AccessDeniedHttpException();
        }
        $quiz = $this->get('zco_quiz.manager.quiz')->get($id);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }
        $quiz->visible = $status ? 1 : 0;
        $quiz->save();

        return redirect(
            $status ? 'Le quiz est maintenant visible par tout le monde.' : 'Le quiz a été masqué.',
            $this->generateUrl('zco_quiz_admin')
        );
    }

    /**
     * Déplacer une question d'un quiz à un autre.
     *
     * @author mwsaz <mwsaz@zcorrecteurs.fr>
     * @param int $id Quiz identifier.
     * @return Response
     */
    public function moveQuestionAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get('zco_quiz.manager.quiz')->getQuestion($id);
        if (!$question) {
            throw new NotFoundHttpException();
        }
        $oldQuiz = $question->Quiz;
        $repository = $this->get('zco_quiz.manager.quiz');

        if (!empty($_POST['quiz'])) {
            $nouveauQuiz = $repository->get($_POST['quiz']);
            if ($nouveauQuiz->id != $oldQuiz->id) {
                $question->quiz_id = $nouveauQuiz->id;
                $question->save();
            }

            return redirect('La question a été déplacée.',
                $this->generateUrl('zco_quiz_editQuiz', ['id' => $oldQuiz['id']])
            );
        }

        $quizList = $repository->lister(true);
        \Page::$titre = 'Déplacer une question';

        return render_to_response('ZcoQuizBundle::moveQuestion.html.php', [
            'question' => $question,
            'quizList' => $quizList,
            'oldQuiz' => $oldQuiz
        ]);
    }

    /**
     * Affiche les statistiques de popularité des quiz, c'est-à-dire tous les quiz
     * classés par nombre de validations, avec diverses informations pour juger de
     * l'intérêt apporté aux membres à chacun des quiz.
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     * @return Response
     */
    public function popularityAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Popularité des quiz';
        $quizList = $this->get('zco_quiz.manager.quiz')->getByPopularity();

        return render_to_response('ZcoQuizBundle::popularity.html.php', [
            'quizList' => $quizList,
        ]);
    }

    /**
     * Affiche des statistiques détaillées sur l'utilisation du module de quiz.
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     */
    public function statsAction($quizId, Request $request)
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        $year = $request->query->get('annee', date('Y'));
        $month = $request->query->get('mois', date('m'));
        $day = $request->query->get('jour');

        if (isset($quizId)) {
            $quiz = $this->get('zco_quiz.manager.quiz')->get($quizId);
            if (!$quiz) {
                throw new NotFoundHttpException();
            }
        }

        list($granularity, $when) = $this->getStatsSpec($year, $month, $day);
        $manager = $this->get('zco_quiz.manager.score');
        $data = $manager->getSummary($granularity, $when, $quizId);

        $previousYear = ($month == 1) ? $year - 1 : $year;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        $previousMonth = ($month == 1) ? 12 : $month - 1;
        $nextMonth = ($month == 12) ? 1 : $month + 1;

        //$donnees = (new StatsService())->construireTableauDonnees($donnees);

        // Statistiques globales (depuis la création des quiz).
        $validationsTotales = $manager->count(QuizScoreManager::ALL, isset($quiz) ? $quiz['id'] : null);
        $validationsMembres = $manager->count(QuizScoreManager::AUTHENTICATED, isset($quiz) ? $quiz['id'] : null);
        $validationsVisiteurs = $manager->count(QuizScoreManager::ANONYMOUS, isset($quiz) ? $quiz['id'] : null);
        $avgNote = $manager->getAverage(QuizScoreManager::ALL, isset($quiz) ? $quiz['id'] : null);

        $quizList = $this->get('zco_quiz.manager.quiz')->lister();
        \Page::$titre = 'Statistiques d\'utilisation des quiz';

        return render_to_response('ZcoQuizBundle::stats.html.php', [
            'annee' => $year,
            'mois' => $month,
            'jour' => $day,
            'donnees' => $data,
            'quiz' => $quiz ?? null,
            'nextYear' => $nextYear,
            'previousYear' => $previousYear,
            'nextMonth' => $nextMonth,
            'previousMonth' => $previousMonth,
            'quizList' => $quizList,
            'validationsTotales' => $validationsTotales,
            'validationsMembres' => $validationsMembres,
            'validationsVisiteurs' => $validationsVisiteurs,
            'noteMoyenne' => $avgNote,
        ]);
    }

    /**
     * Affiche un graphique de statistiques d'utilisation de tous les quiz confondus,
     * ou bien d'un quiz particulier sur n'importe quelle période (soit depuis la
     * création, sur une année, sur un mois ou bien sur une journée).
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     */
    public function statsChartAction($quizId, Request $request)
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        $year = $request->query->get('annee');
        $month = $request->query->get('mois');
        $day = $request->query->get('jour');

        if (isset($quizId)) {
            $quiz = $this->get('zco_quiz.manager.quiz')->get($quizId);
            if (!$quiz) {
                throw new NotFoundHttpException();
            }
        }

        list($granularity, $when) = $this->getStatsSpec($year, $month, $day);
        $data = $this->get('zco_quiz.manager.score')->getSummary($granularity, $when, $quizId);
        $chart = new GlobalStatsChart($data, $granularity, $when);

        return $chart->getResponse();
    }

    private function getStatsSpec($year, $month, $day)
    {
        if (isset($day, $month, $year)) {
            $granularity = QuizScoreManager::DAY;
            $when = [$year, $month, $day];
        } elseif (isset($month, $year)) {
            $granularity = QuizScoreManager::MONTH;
            $when = [$year, $month];
        } elseif (isset($year)) {
            $granularity = QuizScoreManager::YEAR;
            $when = [$year];
        } else {
            $granularity = QuizScoreManager::ALL;
            $when = [];
        }
        return [$granularity, $when];
    }
}