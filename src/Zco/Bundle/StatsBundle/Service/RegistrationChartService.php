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

namespace Zco\Bundle\StatsBundle\Service;

/**
 * Contrôleur pour le graphique des statistiques d'inscription.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class RegistrationChartService
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

    public function draw($classementFils, $classementSql, $annee, $moisDepartDeUn, $jourDepartDeUn)
    {
        $liste = $this->statsService->getRegistrationStats($classementFils, $classementSql, $annee, $moisDepartDeUn, $jourDepartDeUn);
        $convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $convertisseurJour = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
        $convertisseurJourNom = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $data = array();
        $compteur = 0;
        $nomOrdonnee = 'Nombre d\'inscrits';
        foreach ($liste AS $element) {
            $data[$compteur] = $element['nombre_inscriptions'];
            $compteur++;
        }

        $graph = new \awGraph(800, 450);
        $graph->setAntiAliasing(true);

        //On fait la mise en page
        $hautGraph = new \awColor(62, 207, 248, 0);
        $basGraph = new \awColor(85, 214, 251, 0);
        $couleurCourbe = new \awColor(20, 100, 10, 0);
        $graph->setBackgroundGradient(new \awLinearGradient($hautGraph, $basGraph, 0));

        //Légende
        $groupe = new \awPlotGroup;
        $groupe->setPadding(50, 20, 20, 40);
        $groupe->axis->left->title->setFont(new \awTuffy(10));
        $groupe->axis->left->title->setPadding(0, 20, 0, 0);
        $groupe->axis->left->title->set($nomOrdonnee);

        $groupe->axis->bottom->title->setFont(new \awTuffy(10));
        if ($classementFils === 'HOUR') {
            $groupe->axis->bottom->setLabelText($convertisseurJour);
            $groupe->axis->bottom->title->set('Heure');
        } elseif ($classementFils === 'DAY') {
            $groupe->axis->bottom->setLabelText($convertisseurJour);
            $groupe->axis->bottom->title->set('Jour');
        } elseif ($classementFils === 'WEEKDAY') {
            $groupe->axis->bottom->setLabelText($convertisseurJourNom);
            $groupe->axis->bottom->title->set('Jour de la semaine');
        } else {
            $groupe->axis->bottom->setLabelText($convertisseurMois);
            $groupe->axis->bottom->title->set('Mois');
        }

        //On trace la courbe avec les données et on la configure
        $plot = new \awLinePlot($data);
        $plot->setColor($couleurCourbe);
        $plot->setXAxis(\awPlot::BOTTOM);
        $plot->setYAxis(\awPlot::LEFT);

        $groupe->add($plot);
        $graph->add($groupe);

        return $graph->draw(\awGraph::DRAW_RETURN);
    }
}
