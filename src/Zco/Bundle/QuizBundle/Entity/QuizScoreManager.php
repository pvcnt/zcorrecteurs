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

namespace Zco\Bundle\QuizBundle\Entity;

class QuizScoreManager
{
    const ALL = 0;
    const YEAR = 1;
    const MONTH = 2;
    const DAY = 3;
    const AUTHENTICATED = -1;
    const ANONYMOUS = -2;

    private $conn;

    /**
     * Constructor.
     *
     * @param \Doctrine_Connection $conn
     */
    public function __construct(\Doctrine_Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Retourne les dernières notes d'un utilisateur.
     *
     * @param integer $userId
     * @param $limit
     * @return array
     */
    public function find($userId, $limit = 30)
    {
        return $this->conn->fetchAll(
            'select s.note, s.date, q.nom as quiz_nom, q.id as quiz_id, q.difficulte as quiz_difficulte, q.categorie_id as quiz_categorie_id
            from zcov2_quiz_scores s
            left join zcov2_quiz q on s.quiz_id = q.id
            where q.visible = 1 AND s.utilisateur_id = ?
            order by s.date DESC
            limit ' . $limit, [$userId]);
    }

    /**
     * Retourne la distribution des notes.
     *
     * @param integer $userId
     * @param integer $quizId L'id du quiz concerné.
     * @return array (node => frequency)
     */
    public function getDistribution($userId = null, $quizId = null)
    {
        $sql = 'select s.note, count(1) as nb 
            from zcov2_quiz_scores s
            left join zcov2_quiz q on s.quiz_id = q.id
            where q.visible = 1';
        $where = [];
        $params = [];
        if ($userId) {
            $where[] = 's.utilisateur_id = ?';
            $params[] = $userId;
        }
        if ($quizId) {
            $where[] = 's.quiz_id = ?';
            $params[] = $quizId;
        }
        if ($where) {
            $sql .= ' and ' . implode(' and ', $where);
        }
        $sql .= ' group by s.note';
        $rows = $this->conn->fetchAll($sql, $params);

        $notes = [];
        foreach ($rows as $row) {
            $notes[(int)$row['note']] = (int)$row['nb'];
        }
        for ($i = 0; $i <= 20; $i++) {
            if (!isset($notes[$i])) {
                $notes[$i] = 0;
            }
        }
        ksort($notes);

        return $notes;
    }

    public function getSummary($granularity = self::ALL, array $when = [], $quizId = null)
    {
        $sql = 'select (MONTH(s.date) - 1) AS mois, COUNT(*) AS validations_totales, 
                AVG(s.note) AS note_moyenne, 
                SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, 
                SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs
                from zcov2_quiz_scores s 
                left join zcov2_quiz q on s.quiz_id = q.id
                where q.visible = 1';
        if ($granularity === self::YEAR) {
            $where = ['YEAR(s.date) = ?'];
            $params = $when[0];
            $groupBy = ['MONTH(s.date)'];
        } elseif ($granularity === self::MONTH) {
            $where = ['YEAR(s.date) = ?', 'MONTH(s.date) = ?'];
            $params = [$when[0], $when[1]];
            $groupBy = ['DAY(s.date)'];
        } elseif ($granularity === self::DAY) {
            $where = ['YEAR(s.date) = ?', 'MONTH(s.date) = ?', 'DAY(s.date) = ?'];
            $params = [$when[0], $when[1], $when[2]];
            $groupBy = ['HOUR(s.date)'];
        } elseif ($granularity === self::ALL) {
            $where = [];
            $params = [];
            $groupBy = ['YEAR(s.date)', 'MONTH(s.date)'];
        } else {
            throw new \InvalidArgumentException('Invalid granularity: ' . $granularity);
        }
        if ($quizId) {
            $where[] = 's.quiz_id = ?';
            $params[] = $quizId;
        }
        if ($where) {
            $sql .= ' and ' . implode(' and ', $where);
        }
        if ($groupBy) {
            $sql .= ' group by ' . implode(', ', $groupBy);
        }

        return $this->conn->fetchAssoc($sql, $params);

        //$this->construireTableauGraphiqueGlobal($rows, 'mois', $debut);
        //return $this->construireTableauGraphique($rows, 'mois', 0, $annee === (int)date('Y') ? (int)date('n') - 1 : 11);
        //$this->construireTableauGraphique($rows, 'jour', 1, ($annee.'-'.$mois) === date('Y-n') ? (int)date('j') : (int)date('t', strtotime($annee.'-'.$mois.'-1')));
        //return $this->construireTableauGraphique($rows, 'heure', 0, ($annee.'-'.$mois.'-'.$jour) === date('Y-n-j') ? (int)date('G') : 23);
    }

    /**
     * Compte le nombre total de validations des quiz.
     *
     * @param int $userId
     * @param int $quizId
     * @return integer
     */
    public function count($userId = null, $quizId = null)
    {
        $sql = 'select count(1) 
            from zcov2_quiz_scores s
            left join zcov2_quiz q on s.quiz_id = q.id
            where q.visible = 1';
        $where = [];
        $params = [];
        if ($userId) {
            if ($userId > 0) {
                $where[] = 's.utilisateur_id = ?';
                $params[] = $userId;
            } elseif ($userId === self::AUTHENTICATED) {
                $where[] = 's.utilisateur_id IS NOT NULL and s.utilisateur_id > 0';
            } elseif ($userId === self::ANONYMOUS) {
                $where[] = 's.utilisateur_id IS NULL or s.utilisateur_id < 0';
            } else {
                throw new \InvalidArgumentException('Invalid user identifier: ' . $userId);
            }
        }
        if ($quizId) {
            $where[] = 's.quiz_id = ?';
            $params[] = $quizId;
        }
        if ($where) {
            $sql .= ' and ' . implode(' and ', $where);
        }

        return $this->conn->fetchOne($sql, $params);
    }

    /**
     * Calcule la moyenne des scores obtenus au quiz.
     *
     * @return float
     */
    public function getAverage($userId = null, $quizId = null)
    {
        $sql = 'select avg(s.note) 
            from zcov2_quiz_scores s
            left join zcov2_quiz q on s.quiz_id = q.id
            where q.visible = 1';
        $where = [];
        $params = [];
        if ($userId) {
            $where[] = 's.utilisateur_id = ?';
            $params[] = $userId;
        }
        if ($quizId) {
            $where[] = 's.quiz_id = ?';
            $params[] = $quizId;
        }
        if ($where) {
            $sql .= ' and ' . implode(' and ', $where);
        }

        return $this->conn->fetchOne($sql, $params);
    }
}
