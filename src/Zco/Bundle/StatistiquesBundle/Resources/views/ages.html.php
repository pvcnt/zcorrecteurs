<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Répartition des membres par âge</h1>

<form method="GET" action="<?php echo $view['router']->path('zco_stats_ages') ?>" class="form-inline">
    <select name="groupe">
        <option style="font-style: italic" value=""<?php if (!$afficherGroupe) echo ' selected="selected"' ?>>Afficher tous les groupes</option>
        <?php foreach($listeGroupes as $groupe): ?>
            <option value="<?php echo $groupe['groupe_id'] ?>"
                <?php if($groupe['groupe_class']) echo ' style="color: '.$groupe['groupe_class'] .'"';
                if($afficherGroupe && $afficherGroupe == $groupe['groupe_id']) echo ' selected="selected"' ?>>
                <?php echo htmlspecialchars($groupe['groupe_nom']) ?>
            </option>
        <?php endforeach ?>
    </select>
    <input type="submit" value="Afficher" class="btn"/>
</form>

<p class="center">
	<img src="<?php echo $view['router']->path('zco_stats_agesChart', ['groupe' => $afficherGroupe]) ?>"
         alt="Âges" />
</p>

<?php if($repartitionAges): ?>
<table class="table table-striped">
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
			<td class="center"><?php echo $tranche ?></td>
			<td class="center"><?php echo $nombre ?></td>
		</tr>
		<?php endforeach ?>
		<tr>
			<td class="center">Non renseigné</td>
			<td class="center"><?php echo $agesInconnus ?></td>
		</tr>
	</tbody>
</table>
<?php endif ?>