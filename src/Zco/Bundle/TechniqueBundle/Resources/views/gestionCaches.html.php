<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gestion des caches</h1>

<p>
	Cette page vous permet de voir tous les fichiers de cache du site, leur date de création et de les supprimer.
</p>

<p class="gras centre">
	<a href="?tout_supprimer=1">Supprimer tous les caches</a><br />
	<a href="?">Enlever les filtres</a>
</p>

<form method="post">
	<fieldset>
		<legend>Filtrer les caches</legend>
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" value="<?php if(!empty($_POST['nom'])) echo htmlspecialchars($_POST['nom']); ?>" /> (<em>* remplace n'importe quels caractères</em>)<br />
		<label for="toujours">Cache n'expirant jamais : </label>
		<input type="checkbox" id="toujours" name="toujours"<?php if(isset($_POST['toujours'])) echo ' checked="checked"'; ?> /><br />
		<input type="submit" name="submit" value="Envoyer" />
	</fieldset>
</form>

<?php
echo '<table class="UI_items">

	<thead>
	<tr class="header_message">
		<th>Nom du cache</th>
		<th>Date de création</th>
		<th>Durée de validité</th>
		<th>Options</th>
	</tr>
	</thead>

	<tbody>';

foreach($caches as $id => $c)
{
	if(!isset($_POST['submit']) || (isset($_POST['submit']) && (!empty($_POST['nom']) ? preg_match('`^'.str_replace('*', '.*', $_POST['nom']).'$`', $id) : true) && (isset($_POST['toujours']) ? (bool)($c['lifetime'] == 0) : true) == true))
	{
		echo '	<tr>
			<td>'.$id.'</td>
			<td class="centre">'.dateformat($c['time']).'</td>
			<td class="centre">';
			if ($c['lifetime'] == 0) { echo 'Toujours'; }
			else { echo $c['lifetime'].' secondes'; }
			echo '</td>
			<td class="centre"><a href="?supprimer='.$id.'"><img src="/img/supprimer.png" alt="Supprimer" /></a></td>
		</tr>';
	}
}

echo '</tbody>
</table>';
