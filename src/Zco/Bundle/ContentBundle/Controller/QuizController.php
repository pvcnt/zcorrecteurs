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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ContentBundle\Chart\GlobalQuizStatsChart;
use Zco\Bundle\ContentBundle\Chart\MyQuizStatsChart;
use Zco\Bundle\ContentBundle\Entity\QuizManager;
use Zco\Bundle\ContentBundle\Entity\QuizScoreManager;
use Zco\Page;

/**
 * Contrôleur gérant les actions liées aux quiz.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author Ziame <ziame@zcorrecteurs.fr>
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
final class QuizController extends Controller
{
    /**
     * Affiche la liste des quiz disponibles.
     *
     * @Route(name="zco_quiz_index", path="/quiz")
     */
    public function indexAction()
    {
        fil_ariane('Quiz');

        return $this->render('ZcoContentBundle:Quiz:index.html.php', [
            'quizList' => $this->get(QuizManager::class)->lister(),
        ]);
    }

    /**
     * Affiche les questions d'un quiz.
     *
     * @Route(name="zco_quiz_show", path="/quiz/{id}/{slug}", requirements={"id":"\d+"})
     *
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function showAction(Request $request, $id, $slug = null)
    {
        $repository = $this->get(QuizManager::class);
        $quiz = $repository->get($id);
        if (!$quiz || !$quiz->visible) {
            throw new NotFoundHttpException();
        }
        $url = $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]);
        if ($slug !== rewrite($quiz['nom'])) {
            // Redirect for SEO if slug is wrong.
            return new RedirectResponse($url, 301);
        }

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

            fil_ariane([
                'Quiz' => $this->generateUrl('zco_quiz_index'),
                htmlspecialchars($quiz['nom']) => $url,
                'Correction',
            ]);

            return $this->render('ZcoContentBundle:Quiz:correction.html.php', [
                'quiz' => $quiz,
                'questions' => $questions,
                'note' => $note,
                'reponses' => $reponses,
            ]);
        }

        Page::$description = htmlspecialchars($quiz['description']);
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($quiz['nom']),
        ]);

        $questions = $repository->findQuestions($quiz['id'], $quiz['aleatoire']);

        return $this->render('ZcoContentBundle:Quiz:show.html.php', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    /**
     * Affiche les statistiques individuelles d'un membre.
     *
     * @Route(name="zco_quiz_myStats", path="/quiz/mes-statistiques")
     */
    public function myStatsAction()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        $repository = $this->get(QuizScoreManager::class);
        $avgNote = $repository->getAverage($_SESSION['id']);
        $nbNotes = $repository->count($_SESSION['id']);
        $lastNotes = $repository->find($_SESSION['id'], 30);

        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Mes statistiques',
        ]);

        return $this->render('ZcoContentBundle:Quiz:myStats.html.php', [
            'avgNote' => $avgNote,
            'nbNotes' => $nbNotes,
            'lastNotes' => $lastNotes,
        ]);
    }

    /**
     * Génère le graphique de statistiques.
     *
     * @Route(name="zco_quiz_myStatsChart", path="/quiz/mes-statistiques.png")
     * @author Ziame <ziame@zcorrecteurs.fr>
     */
    public function myStatsChartAction()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        $distribution = $this->get(QuizScoreManager::class)->getDistribution($_SESSION['id']);

        return (new MyQuizStatsChart($distribution))->getResponse();
    }

    /**
     * Display a list of all quiz, including unpublished ones.
     *
     * @Route(name="zco_quiz_admin", path="/admin/quiz")
     *
     * @return Response
     */
    public function adminAction()
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Gestion des quiz',
        ]);

        $quizList = $this->get(QuizManager::class)->lister(true);

        return $this->render('ZcoContentBundle:Quiz:admin.html.php', [
            'quizList' => $quizList,
        ]);
    }

    /**
     * Add a new question to an existing quiz.
     *
     * @Route(name="zco_quiz_newQuestion", path="/quiz/ajouter-question/{quizId}", requirements={"quizId":"\d+"})
     *
     * @param int $quizId Quiz identifier.
     * @return Response
     */
    public function newQuestionAction($quizId)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $quiz = $this->get(QuizManager::class)->get($quizId);
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

        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]),
            'Nouvelle question'
        ]);

        return $this->render('ZcoContentBundle:Quiz:newQuestion.html.php', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Ajoute un nouveau quiz.
     *
     * @Route(name="zco_quiz_newQuiz", path="/quiz/ajouter")
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
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Nouveau quiz'
        ]);

        return $this->render('ZcoContentBundle:Quiz:newQuiz.html.php', [
            'categories' => $categories,
            'levels' => \Quiz::LEVELS,
        ]);
    }

    /**
     * Edit an existing question of a quiz.
     *
     * @Route(name="zco_quiz_editQuestion", path="/quiz/modifier-question/{id}", requirements={"id":"\d+"})
     *
     * @param int $id Question identifier.
     * @return Response
     */
    public function editQuestionAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get(QuizManager::class)->getQuestion($id);
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

        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($question->Quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $question->Quiz['id'], 'slug' => rewrite($question->Quiz['nom'])]),
            'Modifier une question'
        ]);

        return $this->render('ZcoContentBundle:Quiz:editQuestion.html.php', [
            'question' => $question,
        ]);
    }

    /**
     * Edit an existing quiz.
     *
     * @Route(name="zco_quiz_editQuiz", path="/quiz/modifier/{id}", requirements={"id":"\d+"})
     *
     * @param int $id Quiz identifier.
     * @return Response
     */
    public function editQuizAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $quizManager = $this->get(QuizManager::class);
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
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]),
            'Modifier'
        ]);

        return $this->render('ZcoContentBundle:Quiz:editQuiz.html.php', [
            'quiz' => $quiz,
            'questions' => $questions,
            'categories' => $categories,
            'levels' => \Quiz::LEVELS,
        ]);
    }

    /**
     * Supprime une question d'un quiz.
     *
     * @Route(name="zco_quiz_deleteQuestion", path="/quiz/supprimer-question/{id}", requirements={"id":"\d+"})
     */
    public function deleteQuestionAction($id, Request $request)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get(QuizManager::class)->getQuestion($id);
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

        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($question->Quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $question->Quiz['id'], 'slug' => rewrite($question->Quiz['nom'])]),
            'Supprimer une question'
        ]);

        return $this->render('ZcoContentBundle:Quiz:deleteQuestion.html.php', [
            'question' => $question,
        ]);
    }

    /**
     * Delete a quiz, after confirmation.
     *
     * @Route(name="zco_quiz_deleteQuiz", path="/quiz/supprimer/{id}", requirements={"id":"\d+"})
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
        $quiz = $this->get(QuizManager::class)->get($id);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() === 'POST') {
            $quiz->delete();

            return redirect('Le quiz a bien été supprimé.', $this->generateUrl('zco_quiz_admin'));
        }

        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]),
            'Supprimer'
        ]);

        return $this->render('ZcoContentBundle:Quiz:deleteQuiz.html.php', array('quiz' => $quiz));
    }

    /**
     * Valide ou dévalide un quiz.
     *
     * @Route(name="zco_quiz_publish", path="/quiz/publier/{id}", requirements={"id":"\d+"})
     *
     * @param int $id Quiz identifier.
     * @param Request $request
     * @return Response
     */
    public function publishQuizAction($id, Request $request)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        if ($request->query->get('token') !== $_SESSION['token']) {
            // CSRF potential problem.
            throw new AccessDeniedHttpException();
        }
        $quiz = $this->get(QuizManager::class)->get($id);
        if (!$quiz) {
            throw new NotFoundHttpException();
        }
        $status = (boolean)$request->get('status', false);
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
     * @Route(name="zco_quiz_moveQuestion", path="/quiz/deplacer-question/{id}", requirements={"id":"\d+"})
     * @author mwsaz <mwsaz@zcorrecteurs.fr>
     *
     * @param int $id Quiz identifier.
     * @return Response
     */
    public function moveQuestionAction($id)
    {
        if (!verifier('quiz_ajouter')) {
            throw new AccessDeniedHttpException();
        }
        $question = $this->get(QuizManager::class)->getQuestion($id);
        if (!$question) {
            throw new NotFoundHttpException();
        }
        $oldQuiz = $question->Quiz;
        $repository = $this->get(QuizManager::class);

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
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            htmlspecialchars($question->Quiz['nom']) => $this->generateUrl('zco_quiz_show', ['id' => $question->Quiz['id'], 'slug' => rewrite($question->Quiz['nom'])]),
            'Déplacer une question'
        ]);

        return $this->render('ZcoContentBundle:Quiz:moveQuestion.html.php', [
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
     * @Route(name="zco_quiz_popularity", path="/quiz/popularite")
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     *
     * @return Response
     */
    public function popularityAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Popularité des quiz'
        ]);

        return $this->render('ZcoContentBundle:Quiz:popularity.html.php', [
            'quizList' => $this->get(QuizManager::class)->getByPopularity(),
        ]);
    }

    /**
     * Affiche des statistiques détaillées sur l'utilisation du module de quiz.
     *
     * @Route(name="zco_quiz_stats", path="/quiz/stats/{quizId}", requirements={"quizId":"\d+"})
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     *
     * @param Request $request.
     * @param int|null $quizId Quiz identifier (null to include all quiz).
     * @return Response
     */
    public function statsAction(Request $request, $quizId = null)
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        $year = $request->query->get('annee', date('Y'));
        $month = $request->query->get('mois', date('m'));
        $day = $request->query->get('jour');

        if (isset($quizId)) {
            $quiz = $this->get(QuizManager::class)->get($quizId);
            if (!$quiz) {
                throw new NotFoundHttpException();
            }
        }

        list($granularity, $when) = $this->getStatsSpec($year, $month, $day);
        $manager = $this->get(QuizScoreManager::class);
        $data = $manager->getSummary($granularity, $when, $quizId);

        $previousYear = ($month == 1) ? $year - 1 : $year;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        $previousMonth = ($month == 1) ? 12 : $month - 1;
        $nextMonth = ($month == 12) ? 1 : $month + 1;

        // Statistiques globales (depuis la création des quiz).
        $validationsTotales = $manager->count(QuizScoreManager::ALL, isset($quiz) ? $quiz['id'] : null);
        $validationsMembres = $manager->count(QuizScoreManager::AUTHENTICATED, isset($quiz) ? $quiz['id'] : null);
        $validationsVisiteurs = $manager->count(QuizScoreManager::ANONYMOUS, isset($quiz) ? $quiz['id'] : null);
        $avgNote = $manager->getAverage(QuizScoreManager::ALL, isset($quiz) ? $quiz['id'] : null);

        $quizList = $this->get(QuizManager::class)->lister();
        fil_ariane([
            'Quiz' => $this->generateUrl('zco_quiz_index'),
            'Statistiques d\'utilisation',
        ]);

        return $this->render('ZcoContentBundle:Quiz:stats.html.php', [
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
     * @Route(name="zco_quiz_statsChart", path="/quiz/stats/{quizId}.png", requirements={"quizId":"\d+"})
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     *
     * @param Request $request.
     * @param int|null $quizId Quiz identifier (null to include all quiz).
     * @return Response
     */
    public function statsChartAction(Request $request, $quizId = null)
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        $year = $request->query->get('annee');
        $month = $request->query->get('mois');
        $day = $request->query->get('jour');

        if (isset($quizId)) {
            $quiz = $this->get(QuizManager::class)->get($quizId);
            if (!$quiz) {
                throw new NotFoundHttpException();
            }
        }

        list($granularity, $when) = $this->getStatsSpec($year, $month, $day);
        $data = $this->get(QuizScoreManager::class)->getSummary($granularity, $when, $quizId);
        $chart = new GlobalQuizStatsChart($data, $granularity, $when);

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

    /**
     * Construit un tableau formaté pour l'affichage des données dans un
     * tableau à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param integer $min_key Clé numérique de départ du tableau.
     * @param integer $max_key Clé numérique de fin du tableau.
     * @return array            Données formatées.
     */
    private function construireTableauDonnees(array $rows, $key, $min_key = null, $max_key = null)
    {
        $ret = array(
            'lignes' => [],
            'totaux' => array(
                'validations_totales' => 0,
                'validations_membres' => 0,
                'validations_visiteurs' => 0,
                'note_moyenne' => 0,
            ));
        //Construction des lignes par défaut si demandé.
        if (isset($min_key) && isset($max_key)) {
            for ($i = $min_key; $i <= $max_key; $i++) {
                $ret['lignes'][$i] = array(
                    'validations_totales' => 0,
                    'validations_membres' => 0,
                    'validations_visiteurs' => 0,
                    'note_moyenne' => 0,
                );
            }
        }
        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $i => $row) {
            if ($row['validations_totales'] > 0) {
                $ret['totaux']['note_moyenne'] = ($ret['totaux']['note_moyenne'] * $ret['totaux']['validations_totales'] + $row['note_moyenne'] * $row['validations_totales']) / ($ret['totaux']['validations_totales'] + $row['validations_totales']);
            }
            $ret['lignes'][$row[$key]] = $row;
            $ret['totaux']['validations_totales'] += $row['validations_totales'];
            $ret['totaux']['validations_membres'] += $row['validations_membres'];
            $ret['totaux']['validations_visiteurs'] += $row['validations_visiteurs'];
        }
        return $ret;
    }
    /**
     * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
     * du quiz à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param integer $min_key Clé numérique de départ du tableau.
     * @param integer $max_key Clé numérique de fin du tableau.
     * @return array            Données formatées.
     */
    private function construireTableauGraphique(array $rows, $key, $min_key = null, $max_key = null)
    {
        $ret = array(
            'validations_totales' => [],
            'validations_membres' => [],
            'validations_visiteurs' => [],
            'note_moyenne' => [],
        );
        //Construction des lignes par défaut si demandé.
        if (isset($min_key) && isset($max_key)) {
            for ($i = $min_key; $i <= $max_key; $i++) {
                $ret['validations_totales'][$i] = 0;
                $ret['validations_membres'][$i] = 0;
                $ret['validations_visiteurs'][$i] = 0;
                $ret['note_moyenne'][$i] = 0;
            }
        }
        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $row) {
            $ret['validations_totales'][$row[$key]] = $row['validations_totales'];
            $ret['validations_membres'][$row[$key]] = $row['validations_membres'];
            $ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
            $ret['note_moyenne'][$row[$key]] = $row['note_moyenne'];
        }
        return $ret;
    }
    /**
     * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
     * du quiz, dans le cas particulier des statistiques globales sans limite de
     *  temps à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param string $debut Date de début, sous la forme annee-mois.
     * @return array            Données formatées.
     */
    private function construireTableauGraphiqueGlobal(array $rows, $key, $debut = null)
    {
        $ret = array(
            'validations_totales' => [],
            'validations_membres' => [],
            'validations_visiteurs' => [],
            'note_moyenne' => [],
        );
        //Construction des lignes par défaut si demandé.
        if (isset($debut)) {
            list($annee_debut, $mois_debut) = explode('-', $debut);
            $mois_debut--;
            $cetteAnnee = date('Y');
            for ($i = $annee_debut; $i <= $cetteAnnee; $i++) {
                $min = ($i == $annee_debut) ? $mois_debut : 0;
                $max = ($i == $cetteAnnee) ? date('m') - 1 : 11;
                for ($j = $min; $j <= $max; $j++) {
                    $ret['validations_totales'][$i . '-' . $j] = 0;
                    $ret['validations_membres'][$i . '-' . $j] = 0;
                    $ret['validations_visiteurs'][$i . '-' . $j] = 0;
                    $ret['note_moyenne'][$i . '-' . $j] = 0;
                }
            }
        }
        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $row) {
            $ret['validations_totales'][$row[$key]] = $row['validations_totales'];
            $ret['validations_membres'][$row[$key]] = $row['validations_membres'];
            $ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
            $ret['note_moyenne'][$row[$key]] = $row['note_moyenne'];
        }
        return $ret;
    }
}