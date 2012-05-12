<?php $view->extend('::layouts/default.html.php') ?>

<h1>Suppression du sujet</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un sujet</legend>
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer le sujet
			<strong><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></strong>
			ainsi que les messages qu'il contient ?<br />

			La suppression entraînera la perte de
			<strong><?php echo $InfosSujet['nombre_de_messages']; ?>
			message<?php echo pluriel($InfosSujet['nombre_de_messages']); ?></strong>.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>