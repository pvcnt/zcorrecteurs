<?php $view->extend('::layouts/default.html.php') ?>

<?php $convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'); ?>

<h1>Rapport d'activité des développeurs</h1>

<p class="centre">
	<img src="/statistiques/graphique-resolution-demandes.html" alt="Rapport d'activité des développeurs annuel" /><br />
	<em>Les anomalies résolues sont représentés par des traits pleins,
	et les tâches implémentées par des pointillés transparents.</em>

</p>

<h2>Activité mensuelle</h2>
<table class="UI_items">
	<thead>
		<tr>
			<td class="centre" colspan="4">
				<a href="?annee=<?php echo $mois == 1 ? $annee - 1 : $annee; ?>&mois=<?php echo $mois == 1 ? 12 : $mois - 1; ?>">&laquo;</a>
				Statistiques du mois de <?php echo mb_strtolower($convertisseurMois[$mois - 1]); ?> <?php echo $annee; ?>
				<a href="?annee=<?php echo $mois == 12 ? $annee + 1 : $annee; ?>&mois=<?php echo $mois == 12 ? 1 : $mois + 1; ?>">&raquo;</a>
			</td>
		</tr>
		<tr>
			<th style="width: 25%;">Pseudo</th>
			<th style="width: 35%;">Groupe</th>
			<th style="width: 20%;">Nb. d'anomalies résolues</th>
			<th style="width: 20%;">Nb. de tâches implémentées</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($donnees_tableau as $d) { ?>
		<tr>
			<td>
				<a href="/membres/profil-<?php echo $d['utilisateur_id']; ?>-<?php echo rewrite($d['utilisateur_pseudo']); ?>.html" style="color: <?php echo $d['groupe_class']; ?>;">
					<?php echo htmlspecialchars($d['utilisateur_pseudo']); ?>
				</a>
			</td>
			<td class="centre"><?php echo $view->get('messages')->afficherGroupe($d) ?><br/></td>
			<td class="centre"><?php echo $d['anomalies']; ?></td>
			<td class="centre"><?php echo $d['taches']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
