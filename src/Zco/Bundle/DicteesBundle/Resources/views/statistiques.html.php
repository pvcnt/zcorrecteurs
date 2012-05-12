<?php $view->extend('::layouts/default.html.php') ?>

<h1>Statistiques de réponse aux dictées</h1>

<form action="" method="post">
	<p>
		Nombre de participations :
		<select name="participations">
			<?php for($i = 5; $i <= 50; $i += 5): ?>
				<option value="<?php echo $i; ?>"<?php
				if($i == $participations)
					echo ' selected="selected"';
				echo '>'.$i.'</option>';
				endfor;
			?>
			</select>

		<input type="submit" value="Afficher" />
	</p>
</form>

<h2>Moyenne</h2>
Votre moyenne est de
<strong><?php echo round($MesStatistiques->moyenne, 1); ?> / 20</strong>
sur <?php echo $MesStatistiques->participations; ?>
 participation<?php echo pluriel($MesStatistiques->participations); ?>.

<h2>Dernières notes</h2>

<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 40%;">Dictée</th>
			<th style="width: 23%;">Difficulté</th>
			<th style="width: 22%;">Date</th>
			<th style="width: 15%;">Note</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($DernieresNotes as $note) { ?>
		<tr>
			<td>
				<a href="dictee-<?php echo $note->Dictee->id; ?>.html">
					<?php echo htmlspecialchars($note->Dictee->titre); ?>
				</a>
			</td>
			<td>
			<span style="float: right; color: <?php
			echo $DicteeCouleurs[$note->Dictee->difficulte]; ?>">
			<?php echo $DicteeDifficultes[$note->Dictee->difficulte]; ?>
			</span>
			<?php echo str_repeat(
				'<img title="'.$DicteeDifficultes[$note->Dictee->difficulte].'"
				alt="'.$DicteeDifficultes[$note->Dictee->difficulte].'"
				src="/img/quiz/etoile.png" />',
				$note->Dictee->difficulte);
			?>
			</td>
			<td><?php echo dateformat($note->date); ?></td>
			<td class="centre"><?php echo $note->note; ?> / 20</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<h2>Statistiques en images</h2>
<p><img src="statistiques-2-<?php echo $participations; ?>.html" alt="Évolution des notes"/></p>
<p><img src="statistiques-1-1.html" alt="Répartition des notes"/></p>
