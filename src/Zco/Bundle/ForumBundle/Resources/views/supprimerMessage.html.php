<?php $view->extend('::layouts/default.html.php') ?>

<h1>Suppression d'un message du sujet</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un message du sujet</legend>
		<p>Êtes-vous sûr de vouloir supprimer ce message ? La suppression est irréversible.</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>