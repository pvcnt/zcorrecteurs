<?php $view->extend('::layouts/default.html.php') ?>

<h1>Reprise de la recorrection d'une soumission depuis Zér0</h1>

<form method="post" action="">
	<fieldset>
		<legend>Reprise de la recorrection d'une soumission depuis Zér0</legend>
		<p class="centre">
			Êtes-vous sûr de vouloir recommencer la <strong>re</strong>correction de cette soumission ?<br />
			Ceci entraînera la perte de toutes les modifications apportées au tutoriel en cours de recorrection.<br />
			<strong>La suppression est irréversible.</strong>
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>
