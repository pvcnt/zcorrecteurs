<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un groupe</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un groupe</legend>
		<p>
			Êtes-vous sûr de vouloir supprimer le groupe <strong><?php echo stripslashes($InfosGroupe['groupe_nom']); ?></strong> ?
			Les <?php echo $InfosGroupe['groupe_effectifs']; ?> membres appartenant à ce groupe seront affectés au groupe par défaut.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<a href="<?php echo $view['router']->path('zco_groups_index') ?>">Non</a>
		</p>
	</fieldset>
</form>