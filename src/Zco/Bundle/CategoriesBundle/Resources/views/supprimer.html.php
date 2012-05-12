<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer une catégorie</h1>

<fieldset>
	<legend>Supprimer une catégorie</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer la catégorie <strong><?php echo htmlspecialchars($InfosCategorie['cat_nom']); ?></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
