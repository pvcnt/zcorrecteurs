<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un message</h1>

<form method="post" action="">
	<fieldset>
		<legend>Supprimer un message</legend>
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer ce message de
			<strong><a href="/membres/profil-<?php echo $msg->Utilisateur['id']; ?>-<?php echo rewrite($msg->Utilisateur['pseudo']); ?>.html"><?php echo htmlspecialchars($msg->Utilisateur['pseudo']); ?></a></strong>
			du livre d'or ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</fieldset>
</form>
