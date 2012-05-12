<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer une question</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer une question</legend>

		<p>Êtes-vous sûr de vouloir supprimer la question intitulée
		« <?php echo $view['messages']->parse($question['nom']) ?> » du sondage
		<strong><?php echo htmlspecialchars($sondage['nom']) ?></strong></p>

		<div class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</fieldset>
</form>
