<?php $view->extend('::layouts/default.html.php') ?>

<h1>Rapport d'activité des zCorrecteurs</h1>

<?php if(verifier('stats_prive')){ ?>
<p class="gras centre">
	<a href="zcorrection.html">Voir les statistiques de zCorrection</a>
</p>
<?php } ?>

<img src="/statistiques/graphique-rapport-zcorrection.html" alt="Graphique représentatif de l'évolution annuelle des corrections" /><br /><br />
<img src="/statistiques/graphique-rapport-zcorrecteurs.html" alt="Graphique représentatif de l'évolution annuelle des corrections par zCorrecteur" />
