<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un utilisateur</h1>
<fieldset>
<legend>Confirmation de la suppression d'un participant</legend>
<p>Confirmez-vous la suppression du participant <strong><?php echo htmlspecialchars($InfoParticipant['utilisateur_pseudo']); ?></strong> ?<br />
Le membre n'aura plus accès au MP, <strong>il ne pourra plus le lire</strong>.</p>
<form action="" method="post">
<div class="centre">
	<input type="submit" name="confirmation" value="Oui" />
	<input type="submit" name="annuler" value="Non" />
</div>
</form>
</fieldset>
