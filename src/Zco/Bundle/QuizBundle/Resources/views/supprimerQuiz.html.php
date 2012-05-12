<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un quiz</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un quiz</legend>
		<p>
			Êtes-vous sûr de vouloir supprimer ce quiz intitulé
			<strong><?php echo htmlspecialchars($InfosQuiz['nom']); ?></strong> ?
			Toutes les questions contenues dedans seront également supprimées, ainsi que les scores associés.
		</p>

		<p class="centre">
			<input type="submit" value="Oui" name="confirmer" />
			<input type="submit" value="Non" name="annuler" />
		</p>
	</fieldset>
</form>