<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * Affiche un graphique de comparaison des performances de
 * plusieurs campagnes (en données volumétriques uniquement : clics,
 * impressions et CTR).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GraphiqueCampagnesAction extends PubliciteActions
{
	public function execute()
	{
		include_once(BASEPATH.'/vendor/Artichow/LinePlot.class.php');
		include_once(BASEPATH.'/vendor/Artichow/Graph.class.php');

		$campagnes = Doctrine_Core::getTable('PubliciteCampagne')->listAll(
			(isset($_GET['all']) && verifier('publicite_voir') ? null : $_SESSION['id']),
			!empty($_GET['etat']) ? $_GET['etat'] : array('en_cours', 'pause', 'termine')
		);

		if (!count($campagnes))
		{
			return new Response('Vous n\'avez pas l\'autorisation de voir les statistiques des publicités.');
		}
		
		$outil = in_array($_GET['type'], array('clic', 'affichage', 'taux')) ? $_GET['type'] : 'clic';
		$interval = 14;
		$dateDebut = isset($_GET['week']) ? strtotime($_GET['week']) : strtotime('-13 days', time());
		$dateFin = strtotime('+'.$interval.' days', $dateDebut);
		$listeJours = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
		$listeMois = array('Jan', 'Fév', 'Mar', 'Avr', 'Mai',
			'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc');
		list($jour, $mois, $annee) = explode('-', date('d-m-Y', $dateDebut));

		$labels = $donnees = $rows = $ids = array();
		foreach ($campagnes as $campagne)
		{
			$ids[] = $campagne['id'];
		}
		$rows_ = Doctrine_Query::create()
			->from('PubliciteStat s')
			->select('s.nb_clics, s.nb_affichages, s.date, p.id')
			->leftJoin('s.Publicite p')
			->where('s.date >= ?', date('Y-m-d', $dateDebut))
			->andWhere('s.date < ?', date('Y-m-d', $dateFin))
			->andWhereIn('p.campagne_id', $ids)
			->orderBy('s.date')
			->execute();
		foreach ($rows_ as $row)
		{
			$rows[$row->Publicite['campagne_id']][$row['date']] = $row;
		}
		unset($rows_);

		for ($i = 0 ; $i < $interval ; $i++)
		{
			$time = strtotime('+'.$i.' days', $dateDebut);
			$labels[] = $listeJours[(int)date('N', $time)-1]."\n".date('j', $time).' '.$listeMois[date('n', $time)-1];
			foreach ($ids as $id)
			{
				if (isset($rows[$id][date('Y-m-d', $time)]))
				{
					if ($outil == 'clic')
						$donnees[$id][] = (int)$rows[$id][date('Y-m-d', $time)]['nb_clics'];
					elseif ($outil == 'affichage')
						$donnees[$id][] = (int)$rows[$id][date('Y-m-d', $time)]['nb_affichages'];
					elseif ($outil == 'taux')
						$donnees[$id][] = round($rows[$id][date('Y-m-d', $time)]['nb_affichages'] > 0 ? (int)$rows[$id][date('Y-m-d', $time)]['nb_clics']*100 / (int)$rows[$id][date('Y-m-d', $time)]['nb_affichages'] : 0, 2);
				}
				else
					$donnees[$id][] = 0;
			}
		}
		array_reverse($labels);

		$graph = new Graph(800, 400);
		$graph->setBackgroundGradient(new LinearGradient(
			new Color(62, 207, 248, 0),
			new Color(85, 214, 251, 0),
			0
		));

		//Légendes des axes.
		$legendes = array('clic' => 'Nombre de clics', 'affichage' => 'Nombre d\'impressions', 'taux' => 'Taux de clics (%)');

		$groupe = new PlotGroup;
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set($legendes[$outil]);

		$groupe->axis->bottom->title->setFont(new Tuffy(10));
		$groupe->axis->bottom->setLabelText($labels);
		$groupe->legend->setPosition(0.35, 0.50);

		//On trace la courbe avec les données.
		$couleurs = array(new Red, new DarkGreen, new Blue, new Black, new Orange, new Purple, new LightGray);
		foreach ($campagnes as $i => $campagne)
		{
			if (empty($_GET['ids']) || in_array($campagne['id'], $_GET['ids']))
			{
				$couleur = $couleurs[$i % count($couleurs)];

				$plot = new LinePlot($donnees[$campagne['id']]);
				$plot->setColor($couleur);
				$plot->setXAxis(Plot::BOTTOM);
				$plot->setYAxis(Plot::LEFT);
				$groupe->add($plot);

				$groupe->legend->add($plot, utf8_decode(htmlspecialchars($campagne['nom'])), Legend::MARK);
			}
		}

		$graph->add($groupe);
		
		$r = new Response($graph->draw(Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		return $r;
	}
}