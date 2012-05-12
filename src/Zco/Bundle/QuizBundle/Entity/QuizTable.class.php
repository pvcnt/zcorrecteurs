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

/**
 */
class QuizTable extends Doctrine_Table
{
	/**
	 * Retourne la liste de tous les quiz, classés par catégorie.
	 *
	 * @return Doctrine_Collection
	 */
	public function lister($tous = false)
	{
		$q = Doctrine_Query::create()
			->select('q.nom, q.description, q.date, q.aleatoire, q.difficulte, '.
				'c.cat_id, c.cat_nom, u.utilisateur_id, u.utilisateur_pseudo, '.
				'(SELECT COUNT(*) FROM QuizQuestion WHERE quiz_id = q.id) AS nb_questions')
			->from('Quiz q')
			->leftJoin('q.Categorie c')
			->leftJoin('q.Utilisateur u')
			->orderBy('c.cat_gauche, q.nom');
		if(!$tous) $q->where('q.visible = 1');
		return $q->execute();
	}

	/**
	 * Récupérer la liste de tous les quiz ordonnés par popularité (c'est-à-dire
	 * par nombre de soumissions du quiz).
	 *
	 * @return array
	 */
	public function listerParPopularite()
	{
		return $this->createQuery('q')
			->select('q.*, COUNT(*) AS validations_totales, '.
				'AVG(s.note) AS note_moyenne, '.
				'(SELECT COUNT(*) FROM QuizQuestion WHERE quiz_id = q.id) AS nb_questions, '.
				'SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, '.
				'SUM(IF(COALESCE(s.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs')
			->leftJoin('q.Scores s')
			->where('q.visible = 1')
			->groupBy('q.id')
			->orderBy('COUNT(*) DESC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	/**
	 * Finder pour trouver les quiz contenant un certain fragment.
	 *
	 * @param string $nom
	 * @return Doctrine_Collection
	 */
	public function findByNom($nom)
	{
		return Doctrine_Query::create()
			->select('q.*, c.id, c.nom')
			->from('Quiz q')
			->leftJoin('q.Categorie c')
			->addWhere('q.nom LIKE ?', '%'.$nom.'%')
			->addWhere('q.visible = 1')
			->orderBy('q.nom')
			->execute();
	}

	/**
	 * Retourne les deux quiz les plus fréquentés sur le dernier mois.
	 * Gère la mise en cache de cette donnée.
	 *
	 * @return Doctrine_Collection
	 */
	public function listerParFrequentation()
	{
		if(!($listeQuizFrequentes = Container::getService('zco_core.cache')->Get('quiz_liste_frequentes')))
		{
			$listeQuizFrequentes = Doctrine_Query::create()
				->select('q.id, q.nom, q.description, q.date, q.difficulte, q.aleatoire, '.
					'(SELECT COUNT(*) FROM QuizQuestion WHERE quiz_id = q.id) AS nb_questions')
				->from('Quiz q')
				->where('s.date > NOW() - INTERVAL 1 MONTH')
				->leftJoin('q.Scores s')
				->where('q.visible = 1')
				->groupBy('quiz_id')
				->orderBy('COUNT(*) DESC')
				->limit(2)
				->execute();
			Container::getService('zco_core.cache')->Set('quiz_liste_frequentes', $listeQuizFrequentes, 86400);
		}
		return $listeQuizFrequentes;
	}

	/**
	 * Retourne deux quiz comportant des questions récemment ajoutées.
	 * Gère la mise en cache de cette donnée.
	 *
	 * @return Doctrine_Collection
	 */
	public function listerRecents()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		if(($listeNouveauxQuiz = Container::getService('zco_core.cache')->Get('quiz_liste_nouveaux')) === false)
		{
			$stmt = $dbh->query('SELECT DISTINCT question.quiz_id AS id, quiz.nom, quiz.description, '
				.'(SELECT COUNT(*) FROM zcov2_quiz_questions WHERE quiz_id = quiz.id) AS nb_questions '
				.'FROM '
					.'(SELECT quiz_id, date '
					.'FROM zcov2_quiz_questions '
					.'ORDER BY date DESC) question '
				.'LEFT JOIN zcov2_quiz quiz '
				.'ON quiz.id = question.quiz_id '
				.'WHERE quiz.visible = 1 '
				.'LIMIT 2');
			$listeNouveauxQuiz = $stmt->fetchAll();
			Container::getService('zco_core.cache')->Set('quiz_liste_nouveaux', $listeNouveauxQuiz, 86400);
		}
		return $listeNouveauxQuiz;
	}

	/**
	 * Retourne un quiz complètement au hasard. Gère la mise en
	 * cache de cette donnée.
	 *
	 * @return Doctrine_Record
	 */
	public function hasard()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		if(!($quizHasard = Container::getService('zco_core.cache')->Get('quiz_quiz_tire_au_hasard')))
		{
			$quizHasard = Doctrine_Query::create()
				->select('q.id, q.nom, q.description, q.date, q.difficulte, q.aleatoire, '.
					'(SELECT COUNT(*) FROM QuizQuestion WHERE quiz_id = q.id) AS nb_questions')
				->from('Quiz q')
				->where('q.visible = 1')
				->orderBy('RAND()')
				->limit(1)
				->fetchOne();
			Container::getService('zco_core.cache')->Set('quiz_quiz_tire_au_hasard', $quizHasard, 86400);
		}
		return $quizHasard;
	}

	/**
	 * Liste simplement les quiz avec leur id et leur nom.
	 *
	 * @return array
	 */
	public function getAllId()
	{
		return $this->createQuery()
			->select('id, nom')
			->where('visible = 1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}
