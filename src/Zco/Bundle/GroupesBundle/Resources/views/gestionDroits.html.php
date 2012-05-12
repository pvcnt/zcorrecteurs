<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gestion des droits</h1>

<p>
	Les droits correpondent à une entité utilisée dans les codes PHP du site. Cette page est donc avant tout destinée aux codeurs.
	Merci de l'utiliser avec précaution, les modifications (suppression notamment) sont irréversibles !<br />
	Il y a actuellement <strong><?php echo count($ListerDroits); ?> droits</strong> enregistrés.
</p>

<p class="gras centre"><a href="ajouter-droit.html">Ajouter un droit</a></p>

<fieldset>
	<legend>Saut rapide</legend>
	<label for="cat">Catégorie : </label>
	<select name="cat" id="cat" onchange="document.location = '#c' + this.value; this.value = 0;">
		<option value="0" class="opt_titre" selected="selected">Sélectionnez</option>
		<?php
		foreach($ListerCategories as $c)
		{
			if($c['cat_niveau'] <= 1)
				echo '<option value="'.$c['cat_id'].'">'.($c['cat_niveau'] > 0 ? '..... ' : '').htmlspecialchars($c['cat_nom']).'</option>';
		}
		?>
	</select>
</fieldset>

<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 25%;">Nom</th>
			<th style="width: 55%;">Description</th>
			<th style="width: 10%;">&Eacute;diter</th>
			<th style="width: 10%;">Supprimer</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$current = null;
		foreach($ListerDroits as $key => $d)
		{
			if($current != $d['cat_id'])
			{
				$current = $d['cat_id'];
				echo '<tr><td colspan="4" class="gras" id="c'.$d['cat_id'].'">'.($d['cat_niveau'] > 0 ? '..... ' : '').htmlspecialchars($d['cat_nom']).'</td><tr>';
			}
		?>
		<tr class="<?php echo $key % 2 ? 'odd' : 'even' ?>">
			<td><?php echo htmlspecialchars($d['droit_nom']); ?></td>
			<td><?php echo htmlspecialchars($d['droit_description']); ?></td>
			<td class="centre"><a href="editer-droit-<?php echo $d['droit_id']; ?>.html"><img src="/img/editer.png" alt="&Eacute;diter" /></a></td>
			<td class="centre"><a href="supprimer-droit-<?php echo $d['droit_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>