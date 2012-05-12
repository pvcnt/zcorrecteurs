<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un recrutement</h1>

<fieldset>
	<legend>Supprimer un recrutement</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer ce recrutement intitulé <strong><?php echo htmlspecialchars($recrutement['nom']); ?></strong> ?<br />
			Toutes les candidatures lui étant liées seront également supprimées.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
