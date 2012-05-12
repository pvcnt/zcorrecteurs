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

/**
 * Statistiques des membres pour leurs dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

define('GRAPHIQUE_FREQUENCE', 1);
define('GRAPHIQUE_EVOLUTION', 2);

include_once(BASEPATH.'/vendor/Artichow/BarPlot.class.php');
include_once(BASEPATH.'/vendor/Artichow/LinePlot.class.php');
include_once(BASEPATH.'/vendor/Artichow/Graph.class.php');

function MesStatistiques()
{
	return Doctrine_Query::create()
		->select('AVG(note) AS moyenne, COUNT(*) AS participations')
		->from('Dictee_Participation dp')
		->leftJoin('dp.Dictee d')
		->addWhere('d.etat = ?', DICTEE_VALIDEE)
		->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
		->execute()
		->offsetGet(0);
}
function DernieresNotes($nombre = 10, $offset = 0)
{
	return Doctrine_Query::create()
		->select('d.id, d.titre, d.difficulte, dp.note, dp.date')
		->from('Dictee_Participation dp')
		->leftJoin('dp.Dictee d')
		->addWhere('d.etat = ?', DICTEE_VALIDEE)
		->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
		->orderBy('dp.date DESC')
		->limit($nombre)
		->offset($offset)
		->execute();
}
function GraphiqueFrequenceNotes()
{
	// Récupération des données
	$d = Doctrine_Query::create()
		->select('dp.note, COUNT(dp.id) AS nombre')
		->from('Dictee_Participation dp')
		->innerJoin('dp.Dictee d')
		->addWhere('d.etat = ?', DICTEE_VALIDEE)
		->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
		->groupBy('dp.note')
		->execute();

	$notes = array();
	for($i = 0; $i <= 20; $i++)
		$notes[$i] = 0;
	foreach($d as $e)
		$notes[$e->note % 21] = $e->nombre;


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
	$groupe->axis->left->title->set('Obtentions');
	$groupe->axis->bottom->title->setFont(new Tuffy(10));
	$groupe->axis->bottom->title->set('Notes');
	$graph->title->set('Répartition des notes (global)');
	$graph->title->setPadding(20, 0, 20, 0);

	// Histogramme
	$plot = new BarPlot($notes);
	$plot->setBarGradient(new LinearGradient(
		$couleurCourbeHaut, $couleurCourbeBas, 0));
	$plot->setXAxis(Plot::BOTTOM);
	$plot->setYAxis(Plot::LEFT);
	$groupe->add($plot);
	$graph->add($groupe);

	return $graph->draw(Graph::DRAW_RETURN);
}

function GraphiqueEvolutionNotes($nombre = 10, $offset = 0)
{
	$nombre = (int)$nombre;
	$nombre < 5 && $nombre = 5;
	$nombre > 50 && $nombre = 50;

	// Récupération des données
	$d = DernieresNotes($nombre, $offset);
	$notes = array();
	foreach($d as $e)
		$notes[] = $e->note;
	for($i = 0; $i <= $nombre; $i++)
		if(!isset($notes[$i]))
			$notes[$i] = 0;
	$notes = array_reverse($notes);

	// Création & mise en page
	$graph = new Graph(800, 450);
	$hautGraph = new Color(62, 207, 248, 0);
	$basGraph = new Color(85, 214, 251, 0);
	$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));

	// Légende
	$groupe = new PlotGroup();
	$groupe->setPadding(50, 20, 20, 40);
	$groupe->axis->left->title->setFont(new Tuffy(10));
	$groupe->axis->left->title->setPadding(0, 20, 0, 0);
	$groupe->axis->left->title->set('Note');
	$groupe->axis->bottom->title->setFont(new Tuffy(10));
	$groupe->axis->bottom->title->set('Temps');
	$groupe->axis->bottom->setLabelNumber(0);
	$graph->title->set('Evolution des notes');
	$graph->title->setPadding(20, 0, 20, 0);

	// Courbe
	$plot = new LinePlot($notes);
	$couleurCourbe = new Color(0, 0, 255);

	$plot->setColor($couleurCourbe);
	$plot->setXAxis(Plot::BOTTOM);
	$plot->setYAxis(Plot::LEFT);
	$plot->mark->setFill($couleurCourbe);

	$groupe->add($plot);
	$graph->add($groupe);

	return $graph->draw(Graph::DRAW_RETURN);
}
