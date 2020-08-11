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

/**
 */
class QuizScoreTable extends Doctrine_Table
{
	/**
	 * Retourne les statistiques individuelles d'un membre
	 * (30 dernières notes, note moyenne et nombre de participations).
	 * @param integer $utilisateur_id
	 * @return array
	 */
	public function StatistiquesMembre($utilisateur_id)
	{
		$stats = array();

		//Dernières notes.
		$stats['notes'] = Doctrine_Query::create()
			->select('s.note, s.date, q.nom, q.id, q.difficulte, q.categorie_id')
			->from('QuizScore s')
			->leftJoin('s.Quiz q')
			->where('q.visible = 1 AND s.utilisateur_id = ?', $utilisateur_id)
			->orderBy('s.date DESC')
			->limit(30)
			->execute();

		//Moyenne.
		$stats['note_moy'] = Doctrine_Query::create()
			->select('AVG(note)')
			->from('QuizScore s')
			->leftJoin('s.Quiz q')
			->where('q.visible = 1 AND s.utilisateur_id = ?', $utilisateur_id)
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

		//Nombre de participations.
		$stats['nb_scores'] = Doctrine_Query::create()
			->select('COUNT(*)')
			->from('QuizScore s')
			->leftJoin('s.Quiz q')
			->where('q.visible = 1 AND s.utilisateur_id = ?', $utilisateur_id)
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

		return $stats;
	}

	/**
	 * Retourne les statistiques de répartition des notes
	 * d'un membre prêtes pour le graphe.
	 *
	 * @param integer $utilisateur_id
	 * @return array
	 */
	public function StatistiquesMembreGraphe($utilisateur_id)
	{
		$ret = Doctrine_Query::create()
			->select('s.note, COUNT(*) AS effectif')
			->from('QuizScore s')
			->leftJoin('s.Quiz q')
			->where('q.visible = 1 AND s.utilisateur_id = ?', $utilisateur_id)
			->groupBy('note')
			->execute();

		$notes = array();
		foreach ($ret as $row)
		{
			$notes[(int)$row['note']] = (int)$row['effectif'];
		}

		for ($i = 0 ; $i <= 20 ; $i++)
		{
			if (!isset($notes[$i])) $notes[$i] = 0;
		}
		ksort($notes);
		return $notes;
	}

	/**
	 * Retourne les données prêtes pour le tracé du graphique de répartition
	 * des notes tous quiz confondus.
	 *
	 * @return array
	 */
	public function getGraphiqueNotes()
	{
		$rows = $this->getStatistiquesNotesQuery()
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireGraphiqueNotes($rows);
	}

	/**
	 * Retourne les données prêtes pour le tracé du graphique de répartition
	 * des notes sur un quiz particulier.
	 *
	 * @param integer $quiz		L'id du quiz concerné.
	 * @return array
	 */
	public function getGraphiqueQuizNotes($quiz)
	{
		$query = $this->getStatistiquesNotesQuery();
		$rows = $query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireGraphiqueNotes($rows);
	}

	/**
	 * Construction de la requête de récupération de la répartition des notes.
	 *
	 * @param Doctrine_Query $q		Éventuellement une requête à modifier.
	 * @return Doctrine_Query
	 */
	protected function getStatistiquesNotesQuery(Doctrine_Query $q = null)
	{
		if (!isset($q))
		{
			$q = $this->createQuery('s');
		}

		return $q
			->addSelect('s.note, COUNT(*) AS effectif')
			->groupBy('s.note');
	}

	/**
	 * Retourne la date de lancement du premier quiz (pour les statistiques
	 * globales sans limite de temps).
	 *
	 * @return string
	 */
	public function getDatePremierQuiz()
	{
		$row = $this->createQuery('s')
			->select('MIN(s.date)')
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $row['MIN'];
	}

	/**
	 * Retourne les données prêtes pour le tracé du graphique d'utilisation
	 * global, sans limite de temps.
	 *
	 * @param string $debut		Date de début du tracé, par défaut celle de création du premier quiz.
	 * @return array
	 */
	public function getGraphiqueGlobal($debut = null)
	{
		if (!isset($debut))
		{
			$debut = $this->getDatePremierQuiz();
		}
		$rows = $this->getStatistiquesGlobalesQuery(null, $debut)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphiqueGlobal($rows, 'mois', $debut);
	}

	/**
	 * Retourne les données prêtes pour le tracé du graphique d'utilisation
	 * global, pour un quiz particulier, sans limite de temps.
	 *
	 * @param integer $quiz		L'id du quiz concerné.
	 * @param string $debut		Date de début du tracé, par défaut celle de création du premier quiz.
	 * @return array
	 */
	public function getGraphiqueQuizGlobal($quiz, $debut = null)
	{
		$query = $this->getStatistiquesGlobalesQuery(null, $debut);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphiqueGlobal($rows, 'mois', $debut);
	}

	/**
	 * Construction de la requête de récupération des données d'utilisation sans
	 * limite de temps.
	 *
	 * @param Doctrine_Query $q		Éventuellement une requête à modifier.
	 * @param string $debut			Date de début.
	 * @return Doctrine_Query
	 */
	protected function getStatistiquesGlobalesQuery(Doctrine_Query $q = null, $debut)
	{
		if (!isset($q))
		{
			$q = $this->createQuery('s');
		}

		return $q
			->select('CONCAT(YEAR('.$q->getRootAlias().'.date), \'-\', (MONTH('.$q->getRootAlias().'.date) - 1)) AS mois, COUNT(*) AS validations_totales, '.
				'AVG('.$q->getRootAlias().'.note) AS note_moyenne, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs')
			->andWhere($q->getRootAlias().'.date >= ?', $debut)
			->groupBy('YEAR('.$q->getRootAlias().'.date)')
			->addGroupBy('MONTH('.$q->getRootAlias().'.date)');
	}

	public function getGraphiqueAnnee($annee)
	{
		$rows = $this->getStatistiquesAnneeQuery(null, $annee)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'mois', 0, $annee === (int)date('Y') ? (int)date('n') - 1 : 11);
	}

	public function getGraphiqueQuizAnnee($quiz, $annee)
	{
		$query = $this->getStatistiquesAnneeQuery(null, $annee);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'mois', 0, $annee === (int)date('Y') ? (int)date('n') - 1 : 11);
	}

	public function getStatistiquesAnneeQuery(Doctrine_Query $q = null, $annee)
	{
		if (!isset($q))
		{
			$q = $this->createQuery('s');
		}

		return $q
			->select('(MONTH('.$q->getRootAlias().'.date) - 1) AS mois, COUNT(*) AS validations_totales, '.
				'AVG('.$q->getRootAlias().'.note) AS note_moyenne, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs')
			->andWhere('YEAR('.$q->getRootAlias().'.date) = ?', $annee)
			->groupBy('MONTH('.$q->getRootAlias().'.date)');
	}

	public function getGraphiqueMois($mois, $annee)
	{
		$rows = $this->getStatistiquesMoisQuery(null, $mois, $annee)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'jour', 1, ($annee.'-'.$mois) === date('Y-n') ? (int)date('j') : (int)date('t', strtotime($annee.'-'.$mois.'-1')));
	}

	public function getGraphiqueQuizMois($quiz, $mois, $annee)
	{
		$query = $this->getStatistiquesMoisQuery(null, $mois, $annee);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'jour', 1, ($annee.'-'.$mois) === date('Y-n') ? (int)date('j') : (int)date('t', strtotime($annee.'-'.$mois.'-1')));
	}

	public function getStatistiquesQuizMois($quiz, $mois, $annee)
	{
		$query = $this->getStatistiquesMoisQuery(null, $mois, $annee);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauDonnees($rows, 'jour', 1, ($annee.'-'.$mois) === date('Y-n') ? (int)date('j') : (int)date('t', strtotime($annee.'-'.$mois.'-1')));
	}

	public function getStatistiquesMois($mois, $annee)
	{
		$rows = $this->getStatistiquesMoisQuery(null, $mois, $annee)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauDonnees($rows, 'jour', 1, ($annee.'-'.$mois) === date('Y-n') ? (int)date('j') : (int)date('t', strtotime($annee.'-'.$mois.'-1')));
	}

	public function getStatistiquesMoisQuery(Doctrine_Query $q = null, $mois, $annee)
	{
		if (!isset($q))
		{
			$q = $this->createQuery('s');
		}

		return $q
			->select('DAY('.$q->getRootAlias().'.date) AS jour, COUNT(*) AS validations_totales, '.
				'AVG('.$q->getRootAlias().'.note) AS note_moyenne, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs')
			->andWhere('MONTH('.$q->getRootAlias().'.date) = ?', $mois)
			->andWhere('YEAR('.$q->getRootAlias().'.date) = ?', $annee)
			->groupBy('DAY('.$q->getRootAlias().'.date)');
	}

	public function getGraphiqueQuizJour($quiz, $jour, $mois, $annee)
	{
		$query = $this->getStatistiquesJourQuery(null, $jour, $mois, $annee);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'heure', 0, ($annee.'-'.$mois.'-'.$jour) === date('Y-n-j') ? (int)date('G') : 23);
	}

	public function getGraphiqueJour($jour, $mois, $annee)
	{
		$rows = $this->getStatistiquesJourQuery(null, $jour, $mois, $annee)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauGraphique($rows, 'heure', 0, ($annee.'-'.$mois.'-'.$jour) === date('Y-n-j') ? (int)date('G') : 23);
	}

	public function getStatistiquesQuizJour($quiz, $jour, $mois, $annee)
	{
		$query = $this->getStatistiquesJourQuery(null, $jour, $mois, $annee);
		$query->andWhere($query->getRootAlias().'.quiz_id = ?', $quiz);
		$rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauDonnees($rows, 'heure', 0, ($annee.'-'.$mois.'-'.$jour) === date('Y-n-j') ? (int)date('G') : 23);
	}

	public function getStatistiquesJour($jour, $mois, $annee)
	{
		$rows = $this->getStatistiquesJourQuery(null, $jour, $mois, $annee)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $this->construireTableauDonnees($rows, 'heure', 0, ($annee.'-'.$mois.'-'.$jour) === date('Y-n-j') ? (int)date('G') : 23);
	}

	public function getStatistiquesJourQuery(Doctrine_Query $q = null, $jour, $mois, $annee)
	{
		if (!isset($q))
		{
			$q = $this->createQuery('s');
		}

		return $q
			->select('HOUR('.$q->getRootAlias().'.date) AS heure, COUNT(*) AS validations_totales, '.
				'AVG('.$q->getRootAlias().'.note) AS note_moyenne, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 1, 0)) AS validations_membres, '.
				'SUM(IF(COALESCE('.$q->getRootAlias().'.utilisateur_id, 0) > 0, 0, 1)) AS validations_visiteurs')
			->andWhere('MONTH('.$q->getRootAlias().'.date) = ?', $mois)
			->andWhere('YEAR('.$q->getRootAlias().'.date) = ?', $annee)
			->andWhere('DAY('.$q->getRootAlias().'.date) = ?', $jour)
			->groupBy('HOUR('.$q->getRootAlias().'.date)');
	}

	/**
	 * Construit un tableau formaté pour le tracé d'un graphique de répartition
	 * des notes à partir de données brutes issues de la requête.
	 *
	 * @param array $rows		Données brutes.
	 * @return array			Données formatées.
	 */
	public function construireGraphiqueNotes(array $rows)
	{
		$ret = array();
		for ($i = 0 ; $i <= 20 ; $i++)
		{
			$ret[$i] = 0;
		}

		foreach ($rows as $row)
		{
			$ret[$row['note']] = $row['effectif'];
		}

		return $ret;
	}

	/**
	 * Construit un tableau formaté pour l'affichage des données dans un
	 * tableau à partir de données brutes issues de la requête.
	 *
	 * @param array $rows		Données brutes.
	 * @param string $key		Clé du futur tableau.
	 * @param integer $min_key	Clé numérique de départ du tableau.
	 * @param integer $max_key	Clé numérique de fin du tableau.
	 * @return array			Données formatées.
	 */
	public function construireTableauDonnees(array $rows, $key, $min_key = null, $max_key = null)
	{
		$ret = array(
			'lignes' => array(),
			'totaux' => array(
				'validations_totales'   => 0,
				'validations_membres'   => 0,
				'validations_visiteurs' => 0,
				'note_moyenne'          => 0,
		));

		//Construction des lignes par défaut si demandé.
		if (isset($min_key) && isset($max_key))
		{
			for ($i = $min_key ; $i <= $max_key ; $i ++)
			{
				$ret['lignes'][$i] = array(
					'validations_totales'   => 0,
					'validations_membres'   => 0,
					'validations_visiteurs' => 0,
					'note_moyenne'          => 0,
				);
			}
		}

		//Remplissage avec les données issues de la base de données.
		foreach ($rows as $i => $row)
		{
			if ($row['validations_totales'] > 0)
			{
				$ret['totaux']['note_moyenne'] = ($ret['totaux']['note_moyenne']*$ret['totaux']['validations_totales']+$row['note_moyenne']*$row['validations_totales'])/($ret['totaux']['validations_totales']+$row['validations_totales']);
			}
			$ret['lignes'][$row[$key]] = $row;
			$ret['totaux']['validations_totales']   += $row['validations_totales'];
			$ret['totaux']['validations_membres']   += $row['validations_membres'];
			$ret['totaux']['validations_visiteurs'] += $row['validations_visiteurs'];
		}

		return $ret;
	}

	/**
	 * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
	 * du quiz à partir de données brutes issues de la requête.
	 *
	 * @param array $rows		Données brutes.
	 * @param string $key		Clé du futur tableau.
	 * @param integer $min_key	Clé numérique de départ du tableau.
	 * @param integer $max_key	Clé numérique de fin du tableau.
	 * @return array			Données formatées.
	 */
	public function construireTableauGraphique(array $rows, $key, $min_key = null, $max_key = null)
	{
		$ret = array(
			'validations_totales'   => array(),
			'validations_membres'   => array(),
			'validations_visiteurs' => array(),
			'note_moyenne'          => array(),
		);

		//Construction des lignes par défaut si demandé.
		if (isset($min_key) && isset($max_key))
		{
			for ($i = $min_key ; $i <= $max_key ; $i ++)
			{
				$ret['validations_totales'][$i]   = 0;
				$ret['validations_membres'][$i]   = 0;
				$ret['validations_visiteurs'][$i] = 0;
				$ret['note_moyenne'][$i]          = 0;
			}
		}

		//Remplissage avec les données issues de la base de données.
		foreach ($rows as $row)
		{
			$ret['validations_totales'][$row[$key]]   = $row['validations_totales'];
			$ret['validations_membres'][$row[$key]]   = $row['validations_membres'];
			$ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
			$ret['note_moyenne'][$row[$key]]          = $row['note_moyenne'];
		}

		return $ret;
	}

	/**
	 * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
	 * du quiz, dans le cas particulier des statistiques globales sans limite de
	 *  temps à partir de données brutes issues de la requête.
	 *
	 * @param array $rows		Données brutes.
	 * @param string $key		Clé du futur tableau.
	 * @param string $debut		Date de début, sous la forme annee-mois.
	 * @return array			Données formatées.
	 */
	public function construireTableauGraphiqueGlobal(array $rows, $key, $debut = null)
	{
		$ret = array(
			'validations_totales'   => array(),
			'validations_membres'   => array(),
			'validations_visiteurs' => array(),
			'note_moyenne'          => array(),
		);

		//Construction des lignes par défaut si demandé.
		if (isset($debut))
		{
			list($annee_debut, $mois_debut) = explode('-', $debut);
			$mois_debut--;

			$cetteAnnee = date('Y');
			for ($i = $annee_debut; $i <= $cetteAnnee ; $i++)
			{
				$min = ($i == $annee_debut) ? $mois_debut : 0;
				$max = ($i == $cetteAnnee) ? date('m') - 1 : 11;

				for ($j = $min; $j <= $max; $j++)
				{
					$ret['validations_totales'][$i.'-'.$j]   = 0;
					$ret['validations_membres'][$i.'-'.$j]   = 0;
					$ret['validations_visiteurs'][$i.'-'.$j] = 0;
					$ret['note_moyenne'][$i.'-'.$j]          = 0;
				}
			}
		}
		//Remplissage avec les données issues de la base de données.
		foreach ($rows as $row)
		{
			$ret['validations_totales'][$row[$key]]   = $row['validations_totales'];
			$ret['validations_membres'][$row[$key]]   = $row['validations_membres'];
			$ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
			$ret['note_moyenne'][$row[$key]]          = $row['note_moyenne'];
		}

		return $ret;
	}

	/**
	 * Compte le nombre total de validations des quiz.
	 *
	 * @param intger $quiz		Si défini, restreindra le compte aux validations de ce qui précis.
	 * @return integer
	 */
	public function compterTotal($quiz = null)
	{
		$query = $this->createQuery('s');
		if (isset($quiz))
		{
			$query->andWhere('s.quiz_id = ?', $quiz);
		}

		return $query->count();
	}

	/**
	 * Compte le nombre total de validations des quiz faites par les membres
	 * inscrits sur le site.
	 *
	 * @param intger $quiz		Si défini, restreindra le compte aux validations de ce qui précis.
	 * @return integer
	 */
	public function compterParMembres($quiz = null)
	{
		$query = $this->createQuery('s');
		if (isset($quiz))
		{
			$query->andWhere('s.quiz_id = ?', (int)$quiz);
		}

		return $query
			->andWhere('s.utilisateur_id IS NOT NULL')
			->andWhere('s.utilisateur_id > 0')
			->count();
	}

	/**
	 * Compte le nombre total de validations des quiz faites par des visiteurs
	 * non inscrits sur le site.
	 *
	 * @param intger $quiz		Si défini, restreindra le compte aux validations de ce qui précis.
	 * @return integer
	 */
	public function compterParVisiteurs($quiz = null)
	{
		$query = $this->createQuery('s');
		if (isset($quiz))
		{
			$query->andWhere('s.quiz_id = ?', $quiz);
		}

		return $query
			->andWhere('s.utilisateur_id IS NULL')
			->orWhere('s.utilisateur_id < 0')
			->count();
	}

	/**
	 * Calcule la moyenne des scores obtenus au quiz.
	 *
	 * @param intger $quiz		Si défini, restreindra le compte aux validations de ce qui précis.
	 * @return integer
	 */
	public function noteMoyenne($quiz = null)
	{
		$query = $this->createQuery('s');
		if (isset($quiz))
		{
			$query->andWhere('s.quiz_id = ?', $quiz);
		}

		return $query
			->select('AVG(s.note)')
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}
}
