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

/*
 * Stats Alexa
 *
 * @author mwsaz@zcorrecteurs.fr
 */

function FetchAlexaRanks($domain = null)
{
	if ($domain === null)
	{
		$domain = URL_SITE;
	}

	$domain = parse_url($domain);
	$domain = $domain['host'];
	if (substr($domain, 0, 4) == 'www.')
	{
		$domain = substr($domain, 4);
	}

	$url = 'http://www.alexa.com/siteinfo/'.$domain;
	$co = file_get_contents($url);

	$ranks = array();

	// RANG MONDIAL
		$pattern = array(
			'&#39;s three-month global Alexa traffic rank is ',
			'.  Search engines refer about'
		);

		$pos = array(
			strpos($co, $pattern[0]),
			strpos($co, $pattern[1])
		);
		$start = $pos[0] + strlen($pattern[0]);
		$end = $pos[1] - $start;

		$rank = substr($co, $start, $end);
		$rank = str_replace(',', '', $rank);
		$ranks['global'] = (int)$rank;

	// RANG EN FRANCE
		$pattern = array(
			'alt="France Flag"/>'."\n",
			'              </div>'
		);

		$pos = array(
			strpos($co, $pattern[0]),
			strpos($co, $pattern[1])
		);
		$start = $pos[0] + strlen($pattern[0]);
		$end = $pos[1] - $start;

		$rank = substr($co, $start, $end);
		$rank = str_replace(',', '', $rank);
		$ranks['france'] = (int)$rank;

	return $ranks;
}

function SaveAlexaRanks($domain = null)
{
	$ranks = FetchAlexaRanks($domain);
	$dbh = Doctrine_Manager::connection()->getDbh();

	$q = $dbh->prepare('INSERT INTO zcov2_statistiques'
		.' (creation, rang_global, rang_france)'
		.' VALUES (CURRENT_TIMESTAMP, ?, ?)');
	$q->execute(array($ranks['global'], $ranks['france']));
}

function GetAlexaRanks($annee, $mois = null)
{
	if ($mois === null) // Toute l'année
	{
		$q = 'SELECT'
			.' CAST(AVG(rang_global) AS UNSIGNED INTEGER) AS rang_global,'
			.' CAST(AVG(rang_france) AS UNSIGNED INTEGER) AS rang_france,'
			.' MONTH(creation) AS mois'

			.' FROM zcov2_statistiques'
			.' WHERE YEAR(creation) = '.(int)$annee
			.' GROUP BY mois'
			.' ORDER BY mois ASC';
	}
	else // Un mois en particulier
	{
		$q = 'SELECT'
			.' CAST(AVG(rang_global) AS UNSIGNED INTEGER) AS rang_global,'
			.' CAST(AVG(rang_france) AS UNSIGNED INTEGER) AS rang_france,'
			.' DAY(creation) AS jour'

			.' FROM zcov2_statistiques'
			.' WHERE YEAR(creation) = '.(int)$annee
			.' AND MONTH(creation) = '.(int)$mois
			.' GROUP BY jour'
			.' ORDER BY jour ASC';
	}
	return Doctrine_Manager::connection()->getDbh()->query($q)->fetchAll(PDO::FETCH_ASSOC);

}

function DessinerGraphique($annee, $mois = null, $dessinerCourbe = null)
{
	include_once(BASEPATH.'/lib/Artichow/LinePlot.class.php');
	include_once(BASEPATH.'/lib/Artichow/Graph.class.php');

	$i18nMois = array(
		1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
		'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

	$courbes = array(
		'France'  => array('rang_france', new Color(0, 0, 255)),
		'Mondial' => array('rang_global', new Color(255, 0, 0))
	);

	$rangs = GetAlexaRanks($annee, $mois);
	if (!$rangs)
		return file_get_contents(BASEPATH.'/web/img/inconnu.png');

	// Création & mise en page
	$graph = new Graph(800, 450);
	$graph->setAntiAliasing(true);
	$hautGraph = new Color(62, 207, 248, 0);
	$basGraph = new Color(85, 214, 251, 0);
	$graph->setBackgroundGradient(new LinearGradient($hautGraph, $basGraph, 0));

	// Légende
	$groupe = new PlotGroup();
	$groupe->setPadding(55, 60, 20, 40);
	$groupe->axis->left->title->setFont(new Tuffy(10));
	$groupe->axis->left->title->setPadding(0, 30, 0, 0);
	$groupe->axis->bottom->title->setFont(new Tuffy(10));
	$groupe->axis->right->title->setFont(new Tuffy(10));
	$groupe->axis->right->title->setPadding(30, 0, 0, 0);

	$groupe->axis->left->title->set('Classement en France');
	$groupe->axis->right->title->set('Classement mondial');

	$legende = array();
	if ($mois === null)
	{
		$premierMois = $rangs[0]['mois'];
		$dernierMois = end($rangs);
		$dernierMois = $dernierMois['mois'];
		for ($i = $premierMois; $i <= $dernierMois; $i++)
			$legende[] = $i18nMois[$i];
		$titre = 'Mois';

	}
	else
	{
		$premierJour = $rangs[0]['jour'];
		$dernierJour = end($rangs);
		$dernierJour = $dernierJour['jour'];

		for ($i = $premierJour; $i <= $dernierJour; $i++)
			$legende[] = $i;
		$titre = 'Jour';
	}
	$groupe->axis->bottom->setLabelText($legende);

	$groupe->axis->bottom->title->set($titre);

	// Courbes Mondial & France
	$premier = true;
	foreach ($courbes as $legende => $courbe)
	{
		if ($dessinerCourbe && $legende != $dessinerCourbe)
			continue;

		$d = array();
		foreach($rangs as $r)
			$d[] = $r[$courbe[0]];
		$plot = new LinePlot($d);

		$plot->setColor($courbe[1]);
		$plot->setXAxis(Plot::BOTTOM);

		if ($premier)
		{
			$plot->setYAxis(Plot::LEFT);
			$groupe->axis->left->setColor($courbe[1]);
		}
		else
		{
			$plot->setYAxis(Plot::RIGHT);
			$groupe->axis->right->setColor($courbe[1]);
		}

		$groupe->legend->add($plot, $legende, Legend::MARK);
		$groupe->add($plot);
		$graph->add($groupe);
		$premier = false;
	}
	$groupe->legend->setPosition(0.9, 0.5);

	return $graph->draw(Graph::DRAW_RETURN);
}

