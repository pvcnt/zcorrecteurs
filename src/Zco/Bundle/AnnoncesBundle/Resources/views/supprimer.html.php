<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Supprimer une annonce</h1>

<fieldset>
	<legend>Supprimer une annonce</legend>
	<form method="post" action="" class="centre">
		<p>
			Êtes-vous sûr de vouloir supprimer l'annonce <strong><?php echo htmlspecialchars($annonce['nom']); ?></strong> ?
		</p>

		<input type="submit" name="confirmer" value="Oui" />
		<input type="submit" name="annuler" value="Non" />
	</form>
</fieldset>
