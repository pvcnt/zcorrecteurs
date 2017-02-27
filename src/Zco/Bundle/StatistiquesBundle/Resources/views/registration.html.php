<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Statistiques d'inscription</h1>

<p class="center">
    <img src="<?php echo $view['router']->path('zco_stats_registrationChart') ?>"
         alt="Graphique des inscriptions" />
</p>

<br />
Voir les inscriptions :
<a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 11, 'annee' => $annee]) ?>">par mois</a> -
<?php if ($classementPere === 'Jour') { ?>
	<a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 11, 'annee' => $annee, 'mois' => $moisDepartDeUn]) ?>">par jour</a>
	<p class="center">
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee, 'mois' => $moisDepartDeUn, 'jour' => $jourDepartDeUn - 1]) ?>">&laquo;</a>
        Statistiques d'inscription du <?php echo $jourDepartDeUn ?> <?php echo $convertisseurMois[$mois] ?> <?php echo $annee ?>
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee, 'mois' => $moisDepartDeUn, 'jour' => $jourDepartDeUn + 1]) ?>">&raquo;</a>
    </p>
<?php } elseif ($classementPere === 'Mois') { ?>
    <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 11, 'annee' => $annee, 'mois' => $moisDepartDeUn]) ?>">par jour</a>
    <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 12, 'annee' => $annee, 'mois' => $moisDepartDeUn]) ?>">par jour de la semaine</a>
    <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 13, 'annee' => $annee, 'mois' => $moisDepartDeUn]) ?>">par heure</a>
    <p class="center">
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee, 'mois' => $moisDepartDeUn - 1]) ?>">&laquo;</a>
        Statistiques d'inscription de <?php echo $convertisseurMois[$mois] ?> <?php echo $annee ?>
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee, 'mois' => $moisDepartDeUn + 1]) ?>">&raquo;</a>
    </p>
<?php } else { ?>
    <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 12, 'annee' => $annee]) ?>">par jour de la semaine</a>
    <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => 13, 'annee' => $annee]) ?>">par heure</a>
    <p class="center">
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee - 1]) ?>">&laquo;</a>
        Statistiques d'inscription <?php echo $annee ?>
        <a href="<?php echo $view['router']->path('zco_stats_registration', ['type' => '1' . $type, 'annee' => $annee + 1]) ?>">&raquo;</a>
    </p>
<?php } ?>
<br /><br />

<table class="table table-striped">
	<thead>
		<tr><th><?php echo $classementFils ?></th>
			<th>Nombre d'inscrits</th>
			<?php if ($classementPere === 'Jour') {echo '<th>Pourcentage pour le jour en cours</th>';}
			else if ($classementPere === 'Mois') {echo '<th>Pourcentage pour le mois en cours</th>';}
			if ($classementPere === 'Année') {echo '<th>Pourcentage pour l\'année en cours</th>';} ?>
			<th>Pourcentage par rapport au total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($RecupStatistiquesInscription AS $elementStatsInscription)
		{
			echo '<tr class="italic">';
			if ($classementSql === 'HOUR') echo '<td class="center">'.($elementStatsInscription['subdivision'] + 1).'</td>';
			else if ($classementSql === 'DAY') echo '<td class="center"><a href="inscription.html?annee='.$annee.'&mois='.$moisDepartDeUn.'&jour='.($elementStatsInscription['subdivision']+1).'">'.($elementStatsInscription['subdivision'] + 1).'</a></td>';
			else if ($classementSql === 'WEEKDAY') echo'<td class="center">'.$convertisseurJourNom[$elementStatsInscription['subdivision']].'</td>';
			else echo '<td class="center"><a href="inscription.html?annee='.$annee.'&mois='.($elementStatsInscription['subdivision']+1).'">'.$convertisseurMois[$elementStatsInscription['subdivision']].'</a></td>';
			echo '<td class="center">'.$elementStatsInscription['nombre_inscriptions'].'</td>';
			echo '<td class="center">'.$elementStatsInscription['pourcentage_pour_division'].'</td>';
			echo '<td class="center">'.$elementStatsInscription['pourcentage_pour_total'].'</td>';
			echo '</tr>';
		} ?>
	<tr class="bold">
		<td class="center">Somme</td>
		<td class="center"><?php echo $somme['somme_inscriptions'] ?></td>
		<td class="center"><?php echo $somme['somme_ppd'] ?></td>
		<td class="center"><?php echo $somme['somme_ppt'] ?></td>
	</tr>
	<tr class="bold">
		<td class="center">Moyenne</td>
		<td class="center"><?php echo $moyenne['moyenne_inscriptions'] ?></td>
		<td class="center"><?php echo $moyenne['moyenne_ppd'] ?></td>
		<td class="center"><?php echo $moyenne['moyenne_ppt'] ?></td>
	</tr>
	<tr class="bold">
		<td class="center">Minimum</td>
		<td class="center"><?php echo $minimum['minimum_inscriptions'] ?></td>
		<td class="center"><?php echo $minimum['minimum_ppd'] ?></td>
		<td class="center"><?php echo $minimum['minimum_ppt'] ?></td>
	</tr>
	<tr class="bold">
		<td class="center">Maximum</td>
		<td class="center"><?php echo $maximum['maximum_inscriptions'] ?></td>
		<td class="center"><?php echo $maximum['maximum_ppd'] ?></td>
		<td class="center"><?php echo $maximum['maximum_ppt'] ?></td>
	</tr>
	</tbody>
</table>
