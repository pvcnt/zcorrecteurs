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

use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur pour le graphique des statistiques d'utilisation du flux du blog.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class GraphiqueBlogFluxAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		include_once(BASEPATH.'/vendor/Artichow/LinePlot.class.php');
		include_once(BASEPATH.'/vendor/Artichow/Graph.class.php');

		if (!isset($_SESSION['donnees_flux_blog']))
		{
			return new Response('Erreur de l\'interface chaise/clavier.<br />Vous n\'avez pas l\'autorisation de ' .
					'voir les statistiques de consultation du flux du blog.');
		}
		
		$annee = $_SESSION['annee'];
		$periode = $_SESSION['periode'];
		$donnees = $_SESSION['donnees_flux_blog'] ;
		$valeurs = array();

		if($periode == 'mois')
		{
			$titreGraphe = 'Consultation du flux du blog';
			$nomOrdonnee = 'Mois';
			$convertisseur = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
			$m = date('m');
			for($i = 0 ; $i < $m ; $i++)
				$valeurs[$i] = 0;

			foreach($donnees as $d)
			{
				$valeurs[$d['periode']-1] = $d['nb_vues'];
			}
		}
		elseif($periode == 'jour')
		{
			$titreGraphe = 'Consultation du flux du blog';
			$nomOrdonnee = 'Jour';
			$convertisseur = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');

			for($i = 0 ; $i <= 6 ; $i++)
				$valeurs[$i] = 0;

			foreach($donnees as $d)
			{
				if($d['periode'] > 1)
					$valeurs[$d['periode']-2] = $d['nb_vues'];
				elseif($d['periode'] == 1)
					$valeurs[6] = $d['nb_vues'];
			}
		}

		//On créé le graphique
		$graph = new Graph(800, 450);
		$graph->setAntiAliasing(true);

		//On fait la mise en page
		$hautGraph = new Color(62, 207, 248, 0);
		$basGraph = new Color(85, 214, 251, 0);
		$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));
		$graph->title->set($titreGraphe);
		$graph->title->setPadding(20,0,20,0);

		//Légende abscisse-ordonnée
		$groupe = new PlotGroup;
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set('Nombre de visites');

		$groupe->axis->bottom->title->setFont(new Tuffy(10));
		$groupe->axis->bottom->setLabelText($convertisseur);
		$groupe->axis->bottom->title->set($nomOrdonnee);

		//Légende droite
		$groupe->legend->show(TRUE);
		$groupe->legend->setModel(Legend::MODEL_RIGHT);
		$groupe->legend->setPosition(0.25, 0.42);
		$groupe->legend->setTextFont(new Tuffy(8));
		$groupe->legend->shadow->setSize(3);

		//On trace la courbe avec les données et on la configure
		$couleurCourbe = new Color(0, 0, 255, 0);

		$plot = new LinePlot($valeurs);
		$plot->setColor($couleurCourbe);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);
		$plot->mark->setFill($couleurCourbe);
		$plot->mark->setType(Mark::RHOMBUS, 6);
		$groupe->add($plot);

		$graph->add($groupe);
		$r = new Response($graph->draw(Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');

		unset($_SESSION['donnees_flux_blog'], $_SESSION['annee'], $_SESSION['periode']);

		return $r;
	}
}
