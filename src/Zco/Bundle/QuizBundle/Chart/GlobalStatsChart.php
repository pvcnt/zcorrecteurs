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

namespace Zco\Bundle\QuizBundle\Chart;

use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\QuizBundle\Entity\QuizScoreManager;
use Zco\Util\TimeUtils;

class GlobalStatsChart
{
    private $data;
    private $granularity;
    private $when;

    /**
     * Constructor.
     *
     * @param array $data
     * @param int $granularity
     * @param array $when
     */
    public function __construct(array $data, $granularity, $when)
    {
        $this->data = $data;
        $this->granularity = $granularity;
        $this->when = $when;
    }

    public function getResponse()
    {
        $response = new Response($this->draw());
        $response->headers->set('Content-type', 'image/png');

        return $response;
    }

    public function draw()
    {
        if ($this->granularity === QuizScoreManager::DAY) {
            $legende = 'Heures du ' . $this->when[2] . ' ' . lcfirst(TimeUtils::FRENCH_MONTH_NAMES[$this->when[1] - 1]) . ' ' . $this->when[0];
        } elseif ($this->granularity === QuizScoreManager::MONTH) {
            $legende = 'Jours du mois de ' . lcfirst(TimeUtils::FRENCH_MONTH_NAMES[$this->when[1] - 1]) . ' ' . $this->when[0];
            $labels = array_keys($this->data['validations_totales']);
        } elseif ($this->granularity === QuizScoreManager::YEAR) {
            $legende = 'Mois de l\'année ' . $this->when[0];
            if ($this->when[0] === (int)date('Y')) {
                $labels = array_slice(TimeUtils::FRENCH_MONTH_NAMES, 0, (int)date('n'));
            } else {
                $labels = TimeUtils::FRENCH_MONTH_NAMES;
            }
        } elseif ($this->granularity === QuizScoreManager::ALL) {
            $labels = [];
            foreach ($this->data['validations_totales'] as $cle => $valeur) {
                list($annee, $mois) = explode('-', $cle);
                $labels[] = TimeUtils::FRENCH_MONTH_NAMES[(int)$mois] . ' ' . $annee;
            }
        } else {
            throw new \InvalidArgumentException('Invalid granularity: ' . $this->granularity);
        }

        $graph = new \awGraph(700, 400);
        $graph->setAntiAliasing(true);
        $hautGraph = new \awColor(62, 207, 248, 0);
        $basGraph = new \awColor(85, 214, 251, 0);
        $graph->setBackgroundGradient(new \awLinearGradient($hautGraph, $basGraph, 0));

        // Légende
        $groupe = new \awPlotGroup();
        $groupe->setPadding(50, 20, 20, 40);
        $groupe->axis->left->title->setFont(new \awTuffy(10));
        $groupe->axis->left->title->setPadding(0, 20, 0, 0);
        $groupe->axis->left->title->set(isset($quiz) ? 'Validations du quiz ' . htmlspecialchars($quiz['nom']) : 'Validations des quiz');

        if ($this->granularity === QuizScoreManager::ALL) {
            $groupe->axis->bottom->setLabelInterval(6);
        }
        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        if (isset($legende)) {
            $groupe->axis->bottom->title->set($legende);
        }
        if (isset($labels)) {
            $groupe->axis->bottom->setLabelText($labels);
        }

        $plot = new \awLinePlot(array_values($this->data['validations_totales']));
        $plot->setColor(new \awBlue());
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);
        $groupe->add($plot);
        $groupe->legend->add($plot, 'Validations totales');

        $plot = new \awLinePlot(array_values($this->data['validations_visiteurs']));
        $plot->setColor(new \awRed());
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);
        $groupe->add($plot);
        $groupe->legend->add($plot, 'Validations par des visiteurs');

        $graph->add($groupe);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }
}