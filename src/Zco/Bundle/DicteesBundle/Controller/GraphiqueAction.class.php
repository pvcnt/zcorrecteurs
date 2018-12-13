<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\DicteesBundle\Domain\DictationScoreDAO;

/**
 * Graphiques de la progression d'un membre sur les dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class GraphiqueAction extends Controller
{
    const GRAPHIQUE_FREQUENCE = 1;
    const GRAPHIQUE_EVOLUTION = 2;

    public function execute()
    {
        $d = null;
        if ($_GET['id'] == self::GRAPHIQUE_FREQUENCE)
            $d = $this->GraphiqueFrequenceNotes();
        elseif ($_GET['id'] == self::GRAPHIQUE_EVOLUTION)
            $d = $this->GraphiqueEvolutionNotes($_GET['id2']);
        else
            return new RedirectResponse('statistiques.html');

        $Response = new Response($d);
        $Response->headers->set('Content-Type', 'image/png');
        return $Response;
    }

    private function GraphiqueFrequenceNotes()
    {
        include_once(BASEPATH . '/lib/Artichow/BarPlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/LinePlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/Graph.class.php');

        // Récupération des données
        $d = DictationScoreDAO::FrequenceNotes();
        $notes = array();
        for ($i = 0; $i <= 20; $i++)
            $notes[$i] = 0;
        foreach ($d as $e)
            $notes[$e->note % 21] = $e->nombre;


        // Création & mise en page
        $graph = new \awGraph(800, 450);
        $hautGraph = new \awColor(62, 207, 248, 0);
        $basGraph = new \awColor(85, 214, 251, 0);
        $couleurCourbeHaut = new \awColor(100, 100, 255, 0);
        $couleurCourbeBas = new \awColor(150, 150, 255, 0);
        $graph->setBackgroundGradient(new \awLinearGradient($hautGraph, $basGraph, 0));

        // Légende
        $groupe = new \awPlotGroup;
        $groupe->setPadding(50, 20, 20, 40);
        $groupe->axis->left->title->setFont(new \awTuffy(10));
        $groupe->axis->left->title->setPadding(0, 20, 0, 0);
        $groupe->axis->left->title->set('Obtentions');
        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        $groupe->axis->bottom->title->set('Notes');
        $graph->title->set('Répartition des notes (global)');
        $graph->title->setPadding(20, 0, 20, 0);

        // Histogramme
        $plot = new \awBarPlot($notes);
        $plot->setBarGradient(new \awLinearGradient(
            $couleurCourbeHaut, $couleurCourbeBas, 0));
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);
        $groupe->add($plot);
        $graph->add($groupe);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }

    private function GraphiqueEvolutionNotes($nombre = 10, $offset = 0)
    {
        include_once(BASEPATH . '/lib/Artichow/BarPlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/LinePlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/Graph.class.php');

        $nombre = (int)$nombre;
        $nombre < 5 && $nombre = 5;
        $nombre > 50 && $nombre = 50;

        // Récupération des données
        $d = DictationScoreDAO::DernieresNotes($nombre, $offset);
        $notes = array();
        foreach ($d as $e)
            $notes[] = $e->note;
        for ($i = 0; $i <= $nombre; $i++)
            if (!isset($notes[$i]))
                $notes[$i] = 0;
        $notes = array_reverse($notes);

        // Création & mise en page
        $graph = new \awGraph(800, 450);
        $hautGraph = new \awColor(62, 207, 248, 0);
        $basGraph = new \awColor(85, 214, 251, 0);
        $graph->setBackgroundGradient(new \awLinearGradient($hautGraph, $basGraph, 0));

        // Légende
        $groupe = new \awPlotGroup();
        $groupe->setPadding(50, 20, 20, 40);
        $groupe->axis->left->title->setFont(new \awTuffy(10));
        $groupe->axis->left->title->setPadding(0, 20, 0, 0);
        $groupe->axis->left->title->set('Note');
        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        $groupe->axis->bottom->title->set('Temps');
        $groupe->axis->bottom->setLabelNumber(0);
        $graph->title->set('Evolution des notes');
        $graph->title->setPadding(20, 0, 20, 0);

        // Courbe
        $plot = new \awLinePlot($notes);
        $couleurCourbe = new \awColor(0, 0, 255);

        $plot->setColor($couleurCourbe);
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);
        $plot->mark->setFill($couleurCourbe);

        $groupe->add($plot);
        $graph->add($groupe);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }
}
