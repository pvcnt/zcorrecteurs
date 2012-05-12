<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques</h1>

<h2>Affluence des visiteurs</h2>
<p>
	Actuellement le site reçoit en moyenne <?php echo $StatsGA['visits_per_day']; ?> visiteurs uniques
	chaque jour pour <?php echo $StatsGA['views_per_day']; ?> pages vues. Cela a fait
	un total pour le mois dernier de <?php echo $StatsGA['views']; ?> pages vues et
	<?php echo $StatsGA['visits']; ?> visiteurs. Le forum reste en tête des pages les plus fréquentées, et nos visiteurs jouent
	le jeu en rapportant des anomalies et en nous suggérant des améliorations, que nous tentons de mettre en place
	assez vite quand nous le jugeons utile.
</p>

<br />
<h2>Correction de tutoriels</h2>
<p>
	Nous avons reçu au total <?php echo $StatsCorrection['nombre_total_tutos']; ?> tutoriels
	(<?php echo $StatsCorrection['nombre_total_mini_tutos']; ?> mini-tutos et
	<?php echo $StatsCorrection['nombre_total_big_tutos']; ?> big-tutos), que ce soit par les membres eux-mêmes
	lors de <a href="/blog/billet-66-ouverture-du-site-des-zcorrecteurs.html">l'ouverture du site</a>
	ou à la suite du <a href="/blog/billet-160-zcorrection-la-theorie-de-l-evolution.html">rapprochement avec le Site du Zéro</a>.
	Nos zCorrecteurs ont mis en moyenne <?php echo $StatsCorrection['temps_moyen_correction_global']; ?> heures pour
	les corriger, avec un nombre moyen de <?php echo $view['humanize']->numberformat($StatsCorrection['nombre_moyen_global_par_zco']); ?> par
	zCorrecteur. Nous pouvons donc les remercier chaleureusement pour leur superbe travail !
</p>
