<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer définitivement une campagne</h1>

<fieldset>
	<legend>Supprimer une campagne</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer définitivement cette campagne nommée
			<strong><?php echo htmlspecialchars($campagne['nom']) ?></strong> ?
			Notez que vous pouvez aussi changer son état à « Supprimé » ce qui aura
			pour effet de l'archiver, mais de conserver les statistiques. En la supprimant
			définitivement, vous supprimerez aussi toutes les publicités qui lui sont
			attachées, ainsi que les statistiques.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
