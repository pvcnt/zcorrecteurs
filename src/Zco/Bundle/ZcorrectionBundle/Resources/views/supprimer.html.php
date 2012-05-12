<?php $view->extend('::layouts/default.html.php') ?>

<h1>Suppression d'une soumission</h1>

<fieldset>
	<legend>Suppression d'une soumission</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer cette soumission ?<br />
			La suppression entraînera au maximum la perte des trois versions du tutoriel et des deux corrections.<br /><strong>La suppression est irréversible.</strong>
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
