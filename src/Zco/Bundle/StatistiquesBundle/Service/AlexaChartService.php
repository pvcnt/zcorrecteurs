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

class AlexaChartService
{
    private $alexa;

    /**
     * Constructor.
     *
     * @param AlexaStatsService $alexa
     */
    public function __construct(AlexaStatsService $alexa)
    {
        $this->alexa = $alexa;
    }

    public function draw($annee, $mois = null, $dessinerCourbe = null)
    {
        $i18nMois = array(
            1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $courbes = array(
            'France' => array('rang_france', new \awColor(0, 0, 255)),
            'Mondial' => array('rang_global', new \awColor(255, 0, 0))
        );

        $rangs = $this->alexa->find($annee, $mois);
        if (!$rangs) {
            return file_get_contents(BASEPATH . '/web/img/inconnu.png');
        }

        // Création & mise en page
        $graph = new \awGraph(800, 450);
        $graph->setAntiAliasing(true);
        $hautGraph = new \awColor(62, 207, 248, 0);
        $basGraph = new \awColor(85, 214, 251, 0);
        $graph->setBackgroundGradient(new \awLinearGradient($hautGraph, $basGraph, 0));

        // Légende
        $groupe = new \awPlotGroup();
        $groupe->setPadding(55, 60, 20, 40);
        $groupe->axis->left->title->setFont(new \awTuffy(10));
        $groupe->axis->left->title->setPadding(0, 30, 0, 0);
        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        $groupe->axis->right->title->setFont(new \awTuffy(10));
        $groupe->axis->right->title->setPadding(30, 0, 0, 0);

        $groupe->axis->left->title->set('Classement en France');
        $groupe->axis->right->title->set('Classement mondial');

        $legende = array();
        if ($mois === null) {
            $premierMois = $rangs[0]['mois'];
            $dernierMois = end($rangs);
            $dernierMois = $dernierMois['mois'];
            for ($i = $premierMois; $i <= $dernierMois; $i++) {
                $legende[] = $i18nMois[$i];
            }
            $titre = 'Mois';
        } else {
            $premierJour = $rangs[0]['jour'];
            $dernierJour = end($rangs);
            $dernierJour = $dernierJour['jour'];
            for ($i = $premierJour; $i <= $dernierJour; $i++) {
                $legende[] = $i;
            }
            $titre = 'Jour';
        }
        $groupe->axis->bottom->setLabelText($legende);
        $groupe->axis->bottom->title->set($titre);

        // Courbes Mondial & France
        $premier = true;
        foreach ($courbes as $legende => $courbe) {
            if ($dessinerCourbe && $legende != $dessinerCourbe) {
                continue;
            }
            $d = array();
            foreach ($rangs as $r) {
                $d[] = $r[$courbe[0]];
            }
            $plot = new \awLinePlot($d);

            $plot->setColor($courbe[1]);
            $plot->setXAxis(\awPlot::BOTTOM);

            if ($premier) {
                $plot->setYAxis(\awPlot::LEFT);
                $groupe->axis->left->setColor($courbe[1]);
            } else {
                $plot->setYAxis(\awPlot::RIGHT);
                $groupe->axis->right->setColor($courbe[1]);
            }

            $groupe->legend->add($plot, $legende, \awLegend::MARK);
            $groupe->add($plot);
            $graph->add($groupe);
            $premier = false;
        }
        $groupe->legend->setPosition(0.9, 0.5);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }

}