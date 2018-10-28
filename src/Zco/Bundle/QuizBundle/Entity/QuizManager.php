<?php

namespace Zco\Bundle\QuizBundle\Entity;

use Doctrine\Common\Cache\Cache;

class QuizManager
{
    private $conn;
    private $cache;

    /**
     * Constructor.
     *
     * @param $conn
     * @param Cache $cache
     */
    public function __construct(\Doctrine_Connection $conn, Cache $cache)
    {
        $this->conn = $conn;
        $this->cache = $cache;
    }

    /**
     * @param int $id
     * @return \Quiz|false
     */
    public function get($id)
    {
        $rows = $this->conn->query('select * from Quiz where id = ?', [$id]);
        if (!count($rows)) {
            return false;
        }

        return $rows[0];
    }

    /**
     * @param int $id
     * @return \QuizQuestion|false
     */
    public function getQuestion($id)
    {
        $rows = $this->conn->query('select * from QuizQuestion where id = ? limit 1', [$id]);

        return (count($rows) > 0) ? $rows[0] : false;
    }

    /**
     * Récupère les questions liées au quiz.
     *
     * @param int $quizId
     * @param bool|integer|array $random
     * @return \Doctrine_Collection
     */
    public function findQuestions($quizId, $random = false)
    {
        $query = \Doctrine_Query::create()
            ->select('id, date, question, explication, reponse1, reponse2, ' .
                'reponse3, reponse4, reponse_juste')
            ->from('QuizQuestion')
            ->where('quiz_id = ?', $quizId);

        if (is_array($random)) {
            $query->andWhereIn('id', $random);
        } else if ($random) {
            $query->orderBy('RAND()');
            if (is_numeric($random)) {
                $query->limit($random);
            }
        }

        return $query->execute();
    }

    /**
     * Retourne la liste de tous les quiz, classés par catégorie.
     *
     * @return \Doctrine_Collection
     */
    public function lister($tous = false)
    {
        $q = \Doctrine_Query::create()
            ->select('q.nom, q.description, q.date, q.aleatoire, q.difficulte, ' .
                'c.cat_id, c.cat_nom, u.utilisateur_id, u.utilisateur_pseudo')
            ->from('Quiz q')
            ->leftJoin('q.Categorie c')
            ->leftJoin('q.Utilisateur u')
            ->orderBy('c.cat_gauche, q.nom');
        if (!$tous) {
            $q->where('q.visible = 1');
        }

        return $q->execute();
    }

    /**
     * Récupérer la liste de tous les quiz ordonnés par popularité (c'est-à-dire
     * par nombre de soumissions du quiz).
     *
     * @return array
     */
    public function getByPopularity()
    {
        return $this->conn->fetchAll('
            select q.*, COUNT(1) AS validations_totales, 
            AVG(s.note) AS note_moyenne,  
            SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, 
            SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs 
            from zcov2_quiz q
            left join zcov2_quiz_scores s on s.quiz_id = q.id
            where q.visible = 1
            group by q.id
            order by count(1) desc');
    }

    /**
     * Finder pour trouver les quiz contenant un certain fragment.
     *
     * @param string $nom
     * @return \Doctrine_Collection
     */
    public function findByNom($nom)
    {
        return \Doctrine_Query::create()
            ->select('q.*, c.id, c.nom')
            ->from('Quiz q')
            ->leftJoin('q.Categorie c')
            ->addWhere('q.nom LIKE ?', '%' . $nom . '%')
            ->addWhere('q.visible = 1')
            ->orderBy('q.nom')
            ->execute();
    }

    /**
     * Retourne les deux quiz les plus fréquentés sur le dernier mois.
     * Gère la mise en cache de cette donnée.
     *
     * @return \Doctrine_Collection
     */
    public function listerParFrequentation()
    {
        if (!($listeQuizFrequentes = $this->cache->fetch('quiz_liste_frequentes'))) {
            $listeQuizFrequentes = \Doctrine_Query::create()
                ->select('q.id, q.nom, q.description, q.date, q.difficulte, q.aleatoire')
                ->from('Quiz q')
                ->where('s.date > NOW() - INTERVAL 1 MONTH')
                ->leftJoin('q.Scores s')
                ->where('q.visible = 1')
                ->groupBy('quiz_id')
                ->orderBy('COUNT(*) DESC')
                ->limit(2)
                ->execute();
            // Cache for one day.
            $this->cache->save('quiz_liste_frequentes', $listeQuizFrequentes, 86400);
        }
        return $listeQuizFrequentes;
    }

    /**
     * Retourne deux quiz comportant des questions récemment ajoutées.
     * Gère la mise en cache de cette donnée.
     *
     * @return \Doctrine_Collection
     */
    public function listerRecents()
    {
        if (($listeNouveauxQuiz = $this->cache->fetch('quiz_liste_nouveaux')) === false) {
            $listeNouveauxQuiz = $this->conn->fetchAll('SELECT DISTINCT question.quiz_id AS id, quiz.nom, quiz.description '
                . 'FROM '
                . '(SELECT quiz_id, date '
                . 'FROM zcov2_quiz_questions '
                . 'ORDER BY date DESC) question '
                . 'LEFT JOIN zcov2_quiz quiz '
                . 'ON quiz.id = question.quiz_id '
                . 'WHERE quiz.visible = 1 '
                . 'LIMIT 2');
            $this->cache->save('quiz_liste_nouveaux', $listeNouveauxQuiz, 86400);
        }

        return $listeNouveauxQuiz;
    }

    /**
     * Retourne un quiz complètement au hasard. Gère la mise en cache de cette donnée.
     *
     * @return \Doctrine_Record
     */
    public function hasard()
    {
        if (!($quizHasard = $this->cache->fetch('quiz_quiz_tire_au_hasard'))) {
            $quizHasard = \Doctrine_Query::create()
                ->select('q.id, q.nom, q.description, q.date, q.difficulte, q.aleatoire')
                ->from('Quiz q')
                ->where('q.visible = 1')
                ->orderBy('RAND()')
                ->limit(1)
                ->fetchOne();
            $this->cache->save('quiz_quiz_tire_au_hasard', $quizHasard, 86400);
        }
        return $quizHasard;
    }
}