<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\ContentBundle\Chart;

use Symfony\Component\HttpFoundation\Response;

final class MyDictationStatsTemporalChart
{
    private $data;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getResponse()
    {
        $response = new Response($this->draw());
        $response->headers->set('Content-type', 'image/png');

        return $response;
    }

    public function draw()
    {
        include_once(BASEPATH . '/lib/Artichow/BarPlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/LinePlot.class.php');
        include_once(BASEPATH . '/lib/Artichow/Graph.class.php');

        $notes = array();
        foreach ($this->data as $e)
            $notes[] = $e->note;
        for ($i = 0; $i <= count($this->data); $i++)
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