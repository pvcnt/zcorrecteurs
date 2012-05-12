<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un sondage</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un sondage</legend>
		<p>
			Êtes-vous sûr de vouloir supprimer le sondage intitulé
			<strong><?php echo htmlspecialchars($sondage['nom']); ?></strong>
			et comportant <strong><?php echo $sondage['nb_questions'] ?>
			question<?php echo pluriel($sondage['nb_questions']) ?></strong> ?
		</p>

		<div class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</fieldset>
</form>