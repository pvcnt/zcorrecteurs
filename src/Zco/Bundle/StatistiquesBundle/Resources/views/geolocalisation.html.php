<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques de géolocalisation</h1>

<table class="UI_items" style="width: 500px; float: right;">
	<thead>
		<tr>
			<th style="width: 60%;">Provenance</th>
			<th style="width: 40%;">Répartition</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$sous_total = 0;
		foreach($Stats as $pays => $pourcent)
		{
			if($pourcent >= 1) $sous_total += $pourcent;
			elseif($sous_total != 0)
			{
			?>
			<tr class="gras">
				<td>Sous-total des pays représentatifs :</td>
				<td class="centre">
					<?php echo $view['humanize']->numberformat($sous_total); ?> % &nbsp;&nbsp;&nbsp;
					(<?php echo round($sous_total * $NbUtilisateurs / 100); ?>
					membre<?php echo pluriel(round($sous_total * $NbUtilisateurs / 100)); ?>)
				</td>
			</tr>
		<?php $sous_total = 0; }	?>
		<tr>
			<td><?php echo htmlspecialchars($pays); ?></td>
			<td class="centre">
				<?php echo $view['humanize']->numberformat($pourcent); ?> % &nbsp;&nbsp;&nbsp;
				(<?php echo round($pourcent * $NbUtilisateurs / 100); ?>
				membre<?php echo pluriel(round($pourcent * $NbUtilisateurs / 100)); ?>)
			</td>
		</tr>
		<?php } ?>
		<tr class="gras">
			<td>Total :</td>
			<td class="centre">
				100,0 % &nbsp;&nbsp;&nbsp;
				(<?php echo $NbUtilisateurs; ?> membres)
			</td>
		</tr>
	</tbody>
</table>

<img src="/statistiques/graphique-geolocalisation.html" alt="Statistiques de géolocalisation des membres" />