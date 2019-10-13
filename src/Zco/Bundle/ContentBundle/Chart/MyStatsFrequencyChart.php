<?php

namespace Zco\Bundle\ContentBundle\Chart;

use Symfony\Component\HttpFoundation\Response;

final class MyStatsFrequencyChart
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

        // Récupération des données
        $notes = array();
        for ($i = 0; $i <= 20; $i++)
            $notes[$i] = 0;
        foreach ($this->data as $e)
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
}