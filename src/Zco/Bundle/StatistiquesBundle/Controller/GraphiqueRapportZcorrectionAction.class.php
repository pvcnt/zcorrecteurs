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
 * Contrôleur pour le graphique des statistiques de la zcorrection.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class GraphiqueRapportZcorrectionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		include_once(BASEPATH."/vendor/Artichow/LinePlot.class.php");
		include_once(BASEPATH."/vendor/Artichow/Graph.class.php");
		
		if (!isset($_SESSION['nombre_total_corrections_news']) || !isset($_SESSION['nombre_total_recorrections_news'])
		|| !isset($_SESSION['nombre_total_corrections_mini']) || !isset($_SESSION['nombre_total_recorrections_mini'])
		|| !isset($_SESSION['nombre_total_corrections_big']) || !isset($_SESSION['nombre_total_recorrections_big']))
		{
			return new Symfony\Component\HttpFoundation\Response('Vous n\'avez pas l\'autorisation de voir les statistiques de zCorrection.');
		}
		
		$nombre_total_corrections_news = $_SESSION['nombre_total_corrections_news'];
		$nombre_total_recorrections_news = $_SESSION['nombre_total_recorrections_news'];
		$nombre_total_corrections_mini = $_SESSION['nombre_total_corrections_mini'];
		$nombre_total_recorrections_mini = $_SESSION['nombre_total_recorrections_mini'];
		$nombre_total_corrections_big = $_SESSION['nombre_total_corrections_big'];
		$nombre_total_recorrections_big = $_SESSION['nombre_total_recorrections_big'];
		unset($_SESSION['nombre_total_corrections_news']);
		unset($_SESSION['nombre_total_recorrections_news']);
		unset($_SESSION['nombre_total_corrections_mini']);
		unset($_SESSION['nombre_total_recorrections_mini']);
		unset($_SESSION['nombre_total_corrections_big']);
		unset($_SESSION['nombre_total_recorrections_big']);

		$convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$nomOrdonnee = 'Nombre de tutos traités';

		$graph = new Graph(800, 450);
		$graph->setAntiAliasing(true);

		//On fait la mise en page
		$hautGraph = new Color(62, 207, 248, 0);
		$basGraph = new Color(85, 214, 251, 0);
		$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));
		$graph->title->set('Evolution du nombre de corrections pour l\'année en cours');
		$graph->title->setPadding(20,0,20,0);

		//On choisit les couleurs des courbes
		$couleurCourbe1 = new Color(0, 150, 0, 0);
		$couleurCourbe2 = new Color(0, 200, 0, 0);
		$couleurCourbe3 = new Color(0, 0, 255, 0);
		$couleurCourbe4 = new Color(50, 150, 255, 0);
		$couleurCourbe5 = new Color(255, 0, 0, 0);
		$couleurCourbe6 = new Color(255, 150, 50, 0);

		//Légende abscisse-ordonnée
		$groupe = new PlotGroup;
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set($nomOrdonnee);

		$groupe->axis->bottom->title->setFont(new Tuffy(10));
		$groupe->axis->bottom->setLabelText($convertisseurMois);
		$groupe->axis->bottom->title->set('Mois');

		//Légende droite
		$groupe->legend->show(TRUE);
		$groupe->legend->setModel(Legend::MODEL_RIGHT);
		$groupe->legend->setTextFont(new Tuffy(8));
		$groupe->legend->shadow->setSize(3);

		//On trace la courbe avec les données et on la configure
		$plot = new LinePlot($nombre_total_corrections_news);
		$plot->setColor($couleurCourbe1);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'News corrigées', Legend::LINE);
		$groupe->add($plot);

		$plot = new LinePlot($nombre_total_recorrections_news);
		$plot->setColor($couleurCourbe2);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'News recorrigées', Legend::LINE);
		$groupe->add($plot);

		$plot = new LinePlot($nombre_total_corrections_mini);
		$plot->setColor($couleurCourbe3);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'Mini-tutos corrigés', Legend::LINE);
		$groupe->add($plot);

		$plot = new LinePlot($nombre_total_recorrections_mini);
		$plot->setColor($couleurCourbe4);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'Mini-tutos recorrigés', Legend::LINE);
		$groupe->add($plot);

		$plot = new LinePlot($nombre_total_corrections_big);
		$plot->setColor($couleurCourbe5);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'Big-tutos corrigés', Legend::LINE);
		$groupe->add($plot);

		$plot = new LinePlot($nombre_total_recorrections_big);
		$plot->setColor($couleurCourbe6);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$groupe->legend->add($plot, 'Big-tutos recorrigés', Legend::LINE);
		$groupe->add($plot);

		$graph->add($groupe);
		$r = new Response($graph->draw(Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		return $r;
	}
}
