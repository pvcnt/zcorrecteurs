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

/**
 * Graphique des statistiques de géolocalisation.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class LocationChartService
{
    private $statsService;

    /**
     * Constructor.
     *
     * @param UserStatsService $stats
     */
    public function __construct(UserStatsService $stats)
    {
        $this->statsService = $stats;
    }

    public function draw()
    {
        $data = $this->statsService->getLocationStats(1);
        $graph = new \awGraph(500, 400);

        //Ajout d'une ombre portée
        $graph->shadow->setPosition(\awShadow::RIGHT_BOTTOM);
        $graph->shadow->setSize(4);

        //Paramétrage du fond
        $graph->setBackgroundGradient(new \awLinearGradient(
            new \awColor(240, 240, 240, 0),
            new \awWhite, 0));

        //Ajout des valeurs et de leurs labels.
        $pie = new \awPie(array_values($data));
        $pie->setLegend(array_keys($data));

        //Positionnements
        $pie->legend->setPosition(1.45, 0.5);
        $pie->legend->setTextFont(new \awTuffy(10));
        $pie->setCenter(0.35, 0.5);
        $pie->setSize(0.65, 0.65);

        //Affiche les pourcentages avec une précision d'un dixième.
        $pie->setLabelPrecision(1);

        $pie->set3D(5);
        $graph->add($pie);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }
}
