/**
 * Affiche un camembert de répartition des dons
 *
 * @provides vitesse-behavior-dons-pie
 * @requires vitesse-behavior
 */
Behavior.create('dons-pie', function(config)
{
    google.setOnLoadCallback(drawChart);
    function drawChart()
    {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Catégorie');
        data.addColumn('number', 'Montant en euros');
        data.addRows(5);
        data.setValue(0, 0, 'Technique : serveur, noms de domaines');
        data.setValue(0, 1, 35);
        data.setValue(1, 0, 'Frais administratifs courants');
        data.setValue(1, 1, 30);
        data.setValue(2, 0, 'Communication');
        data.setValue(2, 1, 13);
        data.setValue(3, 0, 'Événements et déplacements');
        data.setValue(3, 1, 13);
        data.setValue(4, 0, 'Formation des bénévoles');
        data.setValue(4, 1, 9);
    
        var formatter = new google.visualization.NumberFormat({suffix: '%', fractionDigits: 0});
        formatter.format(data, 1);

        var chart = new google.visualization.PieChart(document.getElementById(config.id));
        chart.draw(data, {width: 330, height: 330, legend: 'none', pieSliceText: 'value', tooltipText: 'percentage', backgroundColor: 'whiteSmoke'});
    }
});