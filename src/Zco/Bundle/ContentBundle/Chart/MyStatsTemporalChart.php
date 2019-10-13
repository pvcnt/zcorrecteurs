<?php

namespace Zco\Bundle\ContentBundle\Chart;

use Symfony\Component\HttpFoundation\Response;

final class MyStatsTemporalChart
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