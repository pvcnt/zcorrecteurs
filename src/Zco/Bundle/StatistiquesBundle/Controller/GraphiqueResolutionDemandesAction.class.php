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
 * Contrôleur pour le graphique des statistiques d'activité des développeurs.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 *         mwsaz <mwsaz@zcorrecteurs.fr>
 */
class GraphiqueResolutionDemandesAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();

		//Inclusion des scripts nécessaires
		include_once(BASEPATH."/vendor/Artichow/LinePlot.class.php");
		include_once(BASEPATH."/vendor/Artichow/Graph.class.php");

		if (!isset($_SESSION['donnees_resolution_demandes']))
		{
			return new Response('Vous n\'avez pas l\'autorisation de voir les statistiques de résolution des demandes.');
		}
		
		$response = new Symfony\Component\HttpFoundation\Response();
		$response->headers->set('Content-Type', 'image/png');

		$donnees = $_SESSION['donnees_resolution_demandes'];
		unset($_SESSION['donnees_resolution_demandes']);

		if (!$donnees)
		{
			readfile(BASEPATH.'/web/img/inconnu.png');
			return $response;
		}

		$convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$nomOrdonnee = 'Nombre de résolutions de demandes';

		//On créé le graphique
		$graph = new Graph(800, 450);
		$graph->setAntiAliasing(true);

		//On fait la mise en page
		$hautGraph = new Color(62, 207, 248, 0);
		$basGraph = new Color(85, 214, 251, 0);
		$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));
		$graph->title->set('Activité des développeurs pour l\'année '.$_SESSION['annee']);
		$graph->title->setPadding(20, 0, 20, 0);

		//Légende abscisse-ordonnée
		$groupe = new PlotGroup();
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

		// Comptage des membres de chaque groupe
		$pasBleu = $pasRouge = $bleus = $rouges = 0;
		$rangeBleu = $rangeRouge = array(0, 255); // 'Largeur' du dégradé
		foreach($donnees as &$e)
		{
			if($e['utilisateur_id_groupe'] == GROUPE_ADMINISTRATEURS)
				$rouges++;
			elseif($e['utilisateur_id_groupe'] == GROUPE_DEVELOPPEURS)
				$bleus++;
		}


		// Calcul du pas dégradé
		($bleus - 1) && $pasBleu = (int)(($rangeBleu[1] - $rangeBleu[0]) / ($bleus - 1));
		($rouges - 1) && $pasRouge = (int)(($rangeRouge[1] - $rangeRouge[0]) / ($rouges - 1));

		// Deux itérations, la première pour les anomalies et la deuxième pour les tâches
		// Traits pleins = anomalies et traits en pointillés = tâches

		// clé => array(style, épaisseur, % transparence)
		$courbes = array(
			'anomalies' => array(Shape::SOLID, 1, 0),
			'taches' => array(Shape::DASHED, 2, 50)
		);
		$premier = true;
		foreach($courbes as $nb => &$style)
		{
			$couleurs = array(
				GROUPE_ADMINISTRATEURS	=> array('R' => 255, 'V' => 0, 'B' => 0),
				GROUPE_DEVELOPPEURS		=> array('R' => 0, 'V' => 0, 'B' => 255)
			);
			foreach($donnees as &$developpeur)
			{
				if($developpeur['utilisateur_id_groupe'] == GROUPE_ADMINISTRATEURS)
				{
					$c = &$couleurs[GROUPE_ADMINISTRATEURS];
					$couleurCourbe = new Color($c['R'], $c['V'], $c['B'], $style[2]);
					$c['V'] += $pasRouge;
					$c['B'] += (int)($pasRouge / 1.5);
				}
				elseif($developpeur['utilisateur_id_groupe'] == GROUPE_DEVELOPPEURS)
				{
					$c = &$couleurs[GROUPE_DEVELOPPEURS];
					$couleurCourbe = new Color($c['R'], $c['V'], $c['B'], $style[2]);
					$c['V'] += $pasBleu;
					$c['R'] += (int)($pasBleu / 1.5);
				}

				$valeurs = array();
				foreach($developpeur['mois'] as $mois => &$stats)
					$valeurs[$mois] = $stats[$nb];

				$plot = new LinePlot($valeurs);
				$plot->setStyle($style[0]);
				$plot->setThickness($style[1]);

				$plot->setColor($couleurCourbe);
				$plot->setXAxis(Plot::BOTTOM);
				$plot->setYAxis(Plot::LEFT);
				$plot->mark->setFill($couleurCourbe);
				$plot->mark->setType(Mark::RHOMBUS, 6);
				if($premier)
					$groupe->legend->add($plot, $developpeur['utilisateur_pseudo'], Legend::MARK);
				$groupe->add($plot);
			}
			$premier = false;
		}

		$graph->add($groupe);
		$response->setContent($graph->draw(Graph::DRAW_RETURN));
		
		return $response;
	}
}
