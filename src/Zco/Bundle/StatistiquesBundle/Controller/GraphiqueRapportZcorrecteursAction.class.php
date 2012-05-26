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

use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur pour le graphique des statistiques d'activité des zcorrecteurs.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class GraphiqueRapportZcorrecteursAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		include_once(BASEPATH."/vendor/Artichow/LinePlot.class.php");
		include_once(BASEPATH."/vendor/Artichow/Graph.class.php");

		if (!isset($_SESSION['donneesTutosParZcorr']))
		{
			return Response('Vous n\'avez pas l\'autorisation de voir les statistiques de zCorrection.');
		}
		
		$donneesTutosParZcorr = $_SESSION['donneesTutosParZcorr'];

		$convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$nomOrdonnee = 'Nombre de tutos traités';

		 // On créé le graphique
		$graph = new Graph(800, 450);
		$graph->setAntiAliasing(true);

		//On fait la mise en page
		$hautGraph = new Color(62, 207, 248, 0);
		$basGraph = new Color(85, 214, 251, 0);
		$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));
		$graph->title->set('Activité des zCorrecteurs pour l\'année en cours');
		$graph->title->setPadding(20,0,20,0);

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
		$groupe->legend->setPosition(0.25, 0.42);
		$groupe->legend->setTextFont(new Tuffy(8));
		$groupe->legend->shadow->setSize(3);

		//On trace la courbe avec les données et on la configure
		foreach ($donneesTutosParZcorr[5] AS $key => $element)
		{
			$pseudo = $key;
			$rouge = rand(200, 255);
			$vert = rand (0, 100);
			$bleu = rand (0, 150);
			$couleurCourbeAdmin = new Color($rouge, $vert, $bleu, 0);
			$plot = new LinePlot($element);
			$plot->setColor($couleurCourbeAdmin);
			$plot->setXAxis(Plot::BOTTOM);
			$plot->setYAxis(Plot::LEFT);
			$plot->mark->setFill($couleurCourbeAdmin);
			$plot->mark->setType(Mark::RHOMBUS, 6);
			// $plot->label->set($pseudo);
			// $plot->label->setBackgroundColor(new Color(0, 0, 0, 100));
			// $plot->label->setColor($couleurCourbeAdmin);
			// $plot->label->setPadding(1, 0, 0, 0);
			// $plot->label->setInterval(2);
			$groupe->legend->add($plot, $pseudo, Legend::MARK);
			$groupe->add($plot);
		}
		foreach ($donneesTutosParZcorr[3] AS $key => $element)
		{
			$pseudo = $key;
			$rouge = rand(0, 200);
			$vert = rand(0, 255);
			$bleu = rand (0, 255);
			$couleurCourbeZcorr = new Color($rouge, $vert, $bleu, 0);
			$plot = new LinePlot($element);
			$plot->setColor($couleurCourbeZcorr);
			$plot->setXAxis(Plot::BOTTOM);
			$plot->setYAxis(Plot::LEFT);
			$plot->mark->setFill($couleurCourbeZcorr);
			$plot->mark->setType(Mark::CIRCLE, 5);
			// $plot->label->set($pseudo);
			// $plot->label->setBackgroundColor(new Color(0, 0, 0, 100));
			// $plot->label->setColor($couleurCourbeZcorr);
			// $plot->label->setPadding(1, 0, 0, 0);
			// $plot->label->setInterval(2);
			$groupe->legend->add($plot, $pseudo, Legend::MARK);
			$groupe->add($plot);
		}

		$graph->add($groupe);
		$r = new Response($graph->draw(Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');
		
		unset($_SESSION['donneesTutosParZcorr']);

		return $r;
	}
}