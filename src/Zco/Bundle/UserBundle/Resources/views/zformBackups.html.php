<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Sauvegardes automatiques de zCode</h1>

<p>
	Les données contenues par un formulaire sont sauvegardées automatique toutes 
	les minutes. Vous pouvez ainsi retrouver ce que vous étiez en train d'écrire 
	en cas de coupure de courant, anomalie, etc. Les sauvegardes sont effacées 
	tous les jours, pour éviter qu'elles ne s'accumulent.
</p>

<?php if (count($backups)): ?>
<table class="table">
	<thead>
		<tr>
			<th>Date</th>
			<th>Formulaire</th>
			<?php if ($textarea): ?>
			<th>Récupérer</th>
			<?php endif ?>
			<th style="width: 60%;">Texte</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($backups as $key => $form): ?>
		<tr>
			<td><?php echo dateformat($form['time']) ?></td>
			<td>
				<a href="<?php echo htmlspecialchars($form['url']) ?>">
					<?php echo htmlspecialchars($form['url']) ?>
				</a>
			</td>
			<?php if ($textarea): ?>
			<td class="center">
				<a href="#" onclick="parent.document.id('<?php echo $textarea ?>').value=document.id('save_<?php echo $key ?>').value; return false;">
					<img src="/img/membres/inserer.png" alt="Récupérer le texte" />
				</a>
			</td>
			<?php endif ?>
			<td>
				<textarea rows="8" id="save_<?php echo $key ?>" style="height: 100px; width: 100%;" onclick="this.select()"><?php echo htmlspecialchars($form['content']) ?></textarea>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>
<p class="italique">Aucune sauvegarde n'a été effectuée depuis hier.</p>
<?php endif ?>
