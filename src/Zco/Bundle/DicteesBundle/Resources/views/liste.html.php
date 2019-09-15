<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des dictées</h1>

<?php if (count($dictations) > 0): ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th>Titre</th>
			<th>Source</th>
			<th>Auteur</th>
			<th>Durée</th>
			<th>Difficulté</th>
			<th>Participations</th>
			<th>Création</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($dictations as $Dictee): ?>
		<tr>
			<td <?php if (!$Dictee->icone) echo 'style="text-indent:55px; height:50px; vertical-align:middle;"'; ?>>
			<?php if ($Dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($Dictee->icone); ?>" height="50" width="50"/>
			<?php endif; ?>
				<a href="dictee-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre); ?>.html">
					<?php echo htmlspecialchars($Dictee->titre); ?>
				</a>
			</td>
			<td>
				<?php echo htmlspecialchars($Dictee->source) ?>
			</td>
			<td>
                <?php if($Dictee->auteur_prenom || $Dictee->auteur_nom): ?>
                    <?php echo htmlspecialchars($Dictee->auteur_prenom) ?>
                    <?php echo htmlspecialchars($Dictee->auteur_nom) ?>
                <?php endif ?>
			</td>
			<td><?php echo $Dictee->temps_estime ?> min</td>
			<td>
			<?php echo str_repeat(
				'<img title="'.$DicteeDifficultes[$Dictee->difficulte].'"
				alt="'.$DicteeDifficultes[$Dictee->difficulte].'"
				src="/bundles/zcoquiz/img/etoile.png" />',
				$Dictee->difficulte);
			?>
			</td>
			<td class="centre"><?php echo $Dictee->participations ?></td>
			<td><?php echo dateformat($Dictee->creation); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>Il n'y a aucune dictée en ligne.</p>
<?php endif; ?>
