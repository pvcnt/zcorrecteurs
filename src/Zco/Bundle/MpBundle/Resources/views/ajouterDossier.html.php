<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un dossier</h1>

<form action="" method="post">
	<fieldset>
		<legend>Ajout d'un dossier</legend>
		<label for="dossier_nom">Nom du dossier : </label>
		<input type="text" name="dossier_nom" id="dossier_nom" size="40" />
		<input type="submit" value="Ajouter" />
	</fieldset>
</form>
