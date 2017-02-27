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

namespace Zco\Bundle\StatistiquesBundle\Service;

class AgesChartService
{
    private $statsService;

    /**
     * Constructor.
     *
     * @param UserStatsService $statsService
     */
    public function __construct(UserStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function draw($groupId = null)
    {
        $data = $this->statsService->getAgeStats($groupId);
        if (!$data) {
            return file_get_contents(BASEPATH . '/web/img/inconnu.png');
        }

        // Réindexer à partir de 0 pour artichow
        $artichowDonnees = array_values($data);
        $artichowLegende = array_keys($data);

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
        $groupe->axis->left->title->set('Effectif');
        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        $groupe->axis->bottom->title->set('Âge (ans)');
        $groupe->axis->bottom->setLabelText($artichowLegende);
        $graph->title->set('Âge des membres');
        $graph->title->setPadding(20, 0, 20, 0);

        // Histogramme
        $plot = new \awBarPlot($artichowDonnees);
        $plot->setBarGradient(new \awLinearGradient(
            $couleurCourbeHaut, $couleurCourbeBas, 0));
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);
        $groupe->add($plot);
        $graph->add($groupe);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }
}