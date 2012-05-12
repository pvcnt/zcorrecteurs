<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Supprimer une réponse</h1>

<form method="post">
	<fieldset>
		<legend>Supprimer la demande</legend>
		<p>&Ecirc;tes-vous sûr de vouloir supprimer cette réponse ?</p>

		<div class="send">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</fieldset>
</form>
