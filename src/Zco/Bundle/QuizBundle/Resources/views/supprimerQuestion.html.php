<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer une question du quiz</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer une question</legend>
		<p>
			Êtes-vous sûr de vouloir supprimer cette question intitulée
			<strong><?php echo htmlspecialchars($InfosQuestion['question']); ?></strong> ?
		</p>
		<p class="centre">
			<input type="submit" value="Oui" name="confirmer" />
			<input type="submit" value="Non" name="annuler" />
		</p>
	</fieldset>
</form>