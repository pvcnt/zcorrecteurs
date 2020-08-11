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
 * Contrôleur pour le graphique des statistiques d'inscription.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class GraphiqueInscriptionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		include_once(BASEPATH."/vendor/Artichow/LinePlot.class.php");
		include_once(BASEPATH."/vendor/Artichow/Graph.class.php");

		if (!isset($_SESSION['graphe_inscription']))
		{
			return new Response('Vous n\'avez pas l\'autorisation de voir les statistiques d\'inscription.');
		}
		
		$liste = $_SESSION['graphe_inscription'];
		$classementFils = $_SESSION['graphe_classementSql'];
		$convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$convertisseurJour = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
		$convertisseurJourNom = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		$data = array(); $compteur = 0;
		$nomOrdonnee = 'Nombre d\'inscrits';
		foreach ($liste AS $element)
		{
			$data[$compteur] = $element['nombre_inscriptions'];
			$compteur++;
		}

		// On créé le graphique
		$graph = new Graph(800, 450);
		$graph->setAntiAliasing(true);

		//On fait la mise en page
		$hautGraph = new Color(62, 207, 248, 0);
		$basGraph = new Color(85, 214, 251, 0);
		$couleurCourbe = new Color(20, 100, 10, 0);
		$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));

		//Légende
		$groupe = new PlotGroup;
		$groupe->setPadding(50, 20, 20, 40);
		$groupe->axis->left->title->setFont(new Tuffy(10));
		$groupe->axis->left->title->setPadding(0, 20, 0, 0);
		$groupe->axis->left->title->set($nomOrdonnee);

		$groupe->axis->bottom->title->setFont(new Tuffy(10));
		if ($classementFils === 'HOUR')
		{$groupe->axis->bottom->setLabelText($convertisseurJour);
		$groupe->axis->bottom->title->set('Heure');}
		elseif ($classementFils === 'DAY')
		{$groupe->axis->bottom->setLabelText($convertisseurJour);
		$groupe->axis->bottom->title->set('Jour');}
		elseif ($classementFils === 'WEEKDAY')
		{$groupe->axis->bottom->setLabelText($convertisseurJourNom);
		$groupe->axis->bottom->title->set('Jour de la semaine');}
		else
		{$groupe->axis->bottom->setLabelText($convertisseurMois);
		$groupe->axis->bottom->title->set('Mois');}

		//On trace la courbe avec les données et on la configure
		$plot = new LinePlot($data);
		$plot->setColor($couleurCourbe);
		$plot->setXAxis(Plot::BOTTOM);
		$plot->setYAxis(Plot::LEFT);

		//Enfin, on mixe le tout
		$groupe->add($plot);
		$graph->add($groupe);

		// On affiche le graphique à l'écran
		$r = new Response($graph->draw(Graph::DRAW_RETURN));
		$r->headers->set('Content-type', 'image/png');
		
		unset($_SESSION['graphe_inscription']);

		return $r;
	}
}
