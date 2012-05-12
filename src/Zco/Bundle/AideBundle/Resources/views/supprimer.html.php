<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer la page « <?php echo htmlspecialchars($page['titre']) ?> »</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un sujet d'aide</legend>
		<p>Voulez vous vraiment supprimer ce sujet d'aide ? La suppression est irréversible.</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>