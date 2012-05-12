<?php $view->extend('::layouts/default.html.php') ?>

<h1>Répartition des membres par âge</h1>

<form method="GET" action="">
	<fieldset>
		<legend>Limiter les résultats à un groupe</legend>
		<label for="input_groupe">Groupe :</label>
		<select name="groupe" id="input_groupe">
			<option style="font-style: italic"
			        value=""<?php if (!$afficherGroupe)
				echo ' selected="selected"' ?>>Afficher tous les groupes</option>
			<?php foreach($listeGroupes as $groupe): ?>
				<option value="<?php echo $groupe['groupe_id']
				?>"<?php if($groupe['groupe_class'])
					echo ' style="color: '.$groupe['groupe_class']
					.'"';
					if($afficherGroupe && $afficherGroupe == $groupe['groupe_id'])
						echo ' selected="selected"' ?>>
					<?php echo htmlspecialchars($groupe['groupe_nom']) ?>
				</option>
			<?php endforeach ?>
		</select>
		<input type="submit" value="Afficher"/>
		<br/>
	</fieldset>
</form>

<p class="centre">
	<img src="graphique-ages-1.html<?php if($afficherGroupe)
		echo '?groupe='.$afficherGroupe ?>" alt="Âges" />
</p>

<?php if($repartitionAges): ?>
<table class="UI_items">
	<thead>
		<tr>
			<th>Tranche d'âge</th>
			<th>Nombre de membres</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($repartitionAges as $tranche => $nombre):
		      if (!$nombre) continue ?>
		<tr>
			<td class="centre"><?php echo $tranche ?></td>
			<td class="centre"><?php echo $nombre ?></td>
		</tr>
		<?php endforeach ?>
		<tr>
			<td class="centre">Non renseigné</td>
			<td class="centre"><?php echo $agesInconnus ?></td>
		</tr>
	</tbody>
</table>
<?php endif ?>

