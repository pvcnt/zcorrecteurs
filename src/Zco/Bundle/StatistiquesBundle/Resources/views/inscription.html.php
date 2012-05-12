<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques d'inscription</h1>

<p style="text-align: center">
    <img src="/statistiques/graphique-inscription.html" alt="Graphique des inscriptions" />
</p>

<?php if ($classementPere === 'Jour')
{
	echo '<br />';
	echo 'Voir les inscriptions : ';
	echo '<a href="inscription.html?type=11&annee='.$annee.'">par mois</a> - <a href="inscription.html?type=11&annee='.$annee.'&mois='.$moisDepartDeUn.'">par jour</a>';		echo '<table class="liste_cat"><caption><a href="inscription.html?type=1'.$type.'&annee='.$annee.'&mois='.$moisDepartDeUn.'&jour='.($jourDepartDeUn - 1).'">&laquo;</a>Statistiques d\'inscription du '.$jourDepartDeUn.' '.$convertisseurMois[$mois].' '.$annee.' <a href="inscription.html?type=1'.$type.'&annee='.$annee.'&mois='.$moisDepartDeUn.'&jour='.($jourDepartDeUn + 1).'.html">&raquo;</a></caption>';
}
else if ($classementPere === 'Mois')
{
	echo '<br />';
	echo 'Voir les inscriptions : ';
	echo '<a href="inscription.html?type=11&annee='.$annee.'">par mois</a> - <a href="inscription.html?type=11&annee='.$annee.'&mois='.$moisDepartDeUn.'">par jour</a> - <a href="inscription.html?type=12&annee='.$annee.'&mois='.$moisDepartDeUn.'">par jour de la semaine</a> - <a href="inscription.html?type=13&annee='.$annee.'&mois='.$moisDepartDeUn.'">par heure</a>.';
	echo '<table class="liste_cat"><caption><a href="inscription.html?type=1'.$type.'&annee='.$annee.'&mois='.($moisDepartDeUn - 1).'">&laquo;</a> Statistiques d\'inscription de '.$convertisseurMois[$mois].' '.$annee.'<a href="inscription.html?type=1'.$type.'&annee='.$annee.'&mois='.($moisDepartDeUn + 1).'">&raquo;</a></caption>';
}
else
{
	echo '<br />';
	echo 'Voir les inscriptions : ';
	echo '<a href="inscription.html?type=11&annee='.$annee.'">par mois</a> - <a href="inscription.html?type=12&annee='.$annee.'">par jour de la semaine</a> - <a href="inscription.html?type=13&annee='.$annee.'">par heure</a>.';
	echo '<table class="liste_cat"><caption><a href="inscription.html?type=1'.$type.'&annee='.($annee - 1).'">&laquo;</a> Statistiques d\'inscription '.$annee.' <a	href="inscription.html?type=1'.$type.'&annee='.($annee + 1).'">&raquo;</a></caption>';
}
?>
<br /><br />
<table class="UI_items">
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
			echo '<tr class="sous_cat">';
			if ($classementSql === 'HOUR') echo '<td class="centre">'.($elementStatsInscription['subdivision'] + 1).'</td>';
			else if ($classementSql === 'DAY') echo '<td class="centre"><a href="inscription.html?annee='.$annee.'&mois='.$moisDepartDeUn.'&jour='.($elementStatsInscription['subdivision']+1).'">'.($elementStatsInscription['subdivision'] + 1).'</a></td>';
			else if ($classementSql === 'WEEKDAY') echo'<td class="centre">'.$convertisseurJourNom[$elementStatsInscription['subdivision']].'</td>';
			else echo '<td class="centre"><a href="inscription.html?annee='.$annee.'&mois='.($elementStatsInscription['subdivision']+1).'">'.$convertisseurMois[$elementStatsInscription['subdivision']].'</a></td>';
			echo '<td class="centre">'.$elementStatsInscription['nombre_inscriptions'].'</td>';
			echo '<td class="centre">'.$elementStatsInscription['pourcentage_pour_division'].'</td>';
			echo '<td class="centre">'.$elementStatsInscription['pourcentage_pour_total'].'</td>';
			echo '</tr>';
		} ?>
	<tr class="bas_tableau">
		<td class="centre">Somme</td>
		<td class="centre"><?php echo $somme['somme_inscriptions'] ?></td>
		<td class="centre"><?php echo $somme['somme_ppd'] ?></td>
		<td class="centre"><?php echo $somme['somme_ppt'] ?></td>
	</tr>
	<tr class="bas_tableau">
		<td class="centre">Moyenne</td>
		<td class="centre"><?php echo $moyenne['moyenne_inscriptions'] ?></td>
		<td class="centre"><?php echo $moyenne['moyenne_ppd'] ?></td>
		<td class="centre"><?php echo $moyenne['moyenne_ppt'] ?></td>
	</tr>
	<tr class="bas_tableau">
		<td class="centre">Minimum</td>
		<td class="centre"><?php echo $minimum['minimum_inscriptions'] ?></td>
		<td class="centre"><?php echo $minimum['minimum_ppd'] ?></td>
		<td class="centre"><?php echo $minimum['minimum_ppt'] ?></td>
	</tr>
	<tr class="bas_tableau">
		<td class="centre">Maximum</td>
		<td class="centre"><?php echo $maximum['maximum_inscriptions'] ?></td>
		<td class="centre"><?php echo $maximum['maximum_ppd'] ?></td>
		<td class="centre"><?php echo $maximum['maximum_ppt'] ?></td>
	</tr>
	</tbody>
</table>
