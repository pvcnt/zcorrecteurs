<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un droit</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un droit</legend>
		&Ecirc;tes-vous sûr de vouloir supprimer le droit <strong><?php echo htmlspecialchars($InfosDroit['droit_nom']); ?></strong>
		(<?php echo htmlspecialchars($InfosDroit['droit_description']); ?>) ?

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>