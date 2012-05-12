<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un MP</h1>

<form action="" method="post">
	<fieldset>
		<legend>Confirmation de la suppression de MP</legend>
		<p>
			Confirmez-vous la suppression du MP
			<strong><?php echo htmlspecialchars($InfoMP['mp_titre']); ?></strong> ? <br />
			La suppression est irréversible.
		</p>

		<div class="centre">
			<input type="submit" name="confirmation" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</fieldset>
</form>
