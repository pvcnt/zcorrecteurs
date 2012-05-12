<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques temporelles du forum</h1>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["areachart"]});
  google.setOnLoadCallback(function() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Mois');
    data.addColumn('number', 'Nombre de messages postés par l\'équipe');
    data.addColumn('number', 'Nombre de messages postés par les membres');
    data.addRows(<?php echo json_encode($StatsGoogleMessages); ?>);

    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, {pointSize: 4, width: '90%', height: 380, min:0, isStacked:true, colors:['#EDE743','#4684EE'], legend: 'top', title: 'Nombre de messages postés par mois'});

    var data2 = new google.visualization.DataTable();
    data2.addColumn('string', 'Mois');
    data2.addColumn('number', 'Nombre de sujets créés au total');
    data2.addRows(<?php echo json_encode($StatsGoogleSujets); ?>);

    var chart2 = new google.visualization.AreaChart(document.getElementById('chart_div2'));
    chart2.draw(data2, {width: '90%', height: 350, min:0, legend: 'top', title: 'Nombre de sujets postés par mois'});
  });
</script>
<div id="chart_div"></div>
<div id="chart_div2"></div>
