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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Graphique de l'âge des membres.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

include_once(BASEPATH.'/vendor/Artichow/BarPlot.class.php');
include_once(BASEPATH.'/vendor/Artichow/Graph.class.php');

class GraphiqueAgesAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre = 'Répartition des membres selon l\'âge';

		$afficherGraphique = isset($_GET['id'])  && $_GET['id'];
		$afficherGroupe    = isset($_GET['groupe'])
		                     && $_GET['groupe'] !== ''
			? (int)$_GET['groupe']
			: null;

		$repartitionAges = array();
		$listeGroupes = ListerGroupes();

		// Limiter le graphique à un groupe
		if ($afficherGroupe !== null)
		{
			$groupeTrouve = false;
			foreach ($listeGroupes as $groupe)
			{
				if ($groupe['groupe_id'] == $afficherGroupe)
				{
					$groupeTrouve = true;
					break;
				}
			}
			if (!$groupeTrouve)
			{
				throw new NotFoundHttpException('Le groupe demandé n\'existe pas.');
			}
		}

		$repartitionAges = Doctrine_Core::getTable('Utilisateur')
			->getAgeMembres($afficherGroupe);

		if (!$afficherGraphique)
		{
			$nombreUtilisateurs = \Doctrine_Core::getTable('Utilisateur')->count();
			$agesInconnus = 0;

			if (is_array($repartitionAges))
			{
				/*// Trier par ordre décroissant selon l'effectif
				natsort($repartitionAges);
				$repartitionAges = array_reverse($repartitionAges);
				*/
				$agesInconnus = $nombreUtilisateurs - array_sum($repartitionAges);
			}

			fil_ariane('Âge des membres');
			return render_to_response(compact('afficherGroupe',
			                                  'listeGroupes',
			                                  'repartitionAges',
			                                  'agesInconnus'));
		}

		if (!$repartitionAges)
		{
			$graphique = file_get_contents(BASEPATH.'/web/img/inconnu.png');
		}
		else
		{
			// Réindexer à partir de 0 pour artichow
			$artichowDonnees = array_values($repartitionAges);
			$artichowLegende = array_keys($repartitionAges);

			// Création & mise en page
			$graph = new Graph(800, 450);
			$hautGraph = new Color(62, 207, 248, 0);
			$basGraph = new Color(85, 214, 251, 0);
			$couleurCourbeHaut = new Color(100, 100, 255, 0);
			$couleurCourbeBas = new Color(150, 150, 255, 0);
			$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));

			// Légende
			$groupe = new PlotGroup;
			$groupe->setPadding(50, 20, 20, 40);
			$groupe->axis->left->title->setFont(new Tuffy(10));
			$groupe->axis->left->title->setPadding(0, 20, 0, 0);
			$groupe->axis->left->title->set('Effectif');
			$groupe->axis->bottom->title->setFont(new Tuffy(10));
			$groupe->axis->bottom->title->set('Âge (ans)');
			$groupe->axis->bottom->setLabelText($artichowLegende);
			$graph->title->set('Âge des membres');
			$graph->title->setPadding(20, 0, 20, 0);

			// Histogramme
			$plot = new BarPlot($artichowDonnees);
			$plot->setBarGradient(new LinearGradient(
				$couleurCourbeHaut, $couleurCourbeBas, 0));
			$plot->setXAxis(Plot::BOTTOM);
			$plot->setYAxis(Plot::LEFT);
			$groupe->add($plot);
			$graph->add($groupe);

			$graphique = $graph->draw(Graph::DRAW_RETURN);
		}

		$r = new Response($graphique);
		$r->headers->set('Content-type', 'image/png');
		
		return $r;
	}
}
