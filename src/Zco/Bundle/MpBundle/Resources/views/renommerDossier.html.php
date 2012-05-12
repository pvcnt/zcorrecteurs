<?php $view->extend('::layouts/default.html.php') ?>

<h1>Renommer un dossier</h1>

<form action="" method="post">
	<fieldset>
		<legend>Édition d'un dossier</legend>
		<label for="dossier_nom">Nom du dossier : </label>
		<input type="text" name="dossier_nom" id="dossier_nom" size="40" value="<?php echo htmlspecialchars($DossierExiste['mp_dossier_titre']); ?>" />
		<input type="submit" value="Renommer" />
	</fieldset>
</form>