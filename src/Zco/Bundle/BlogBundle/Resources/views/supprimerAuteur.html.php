<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un auteur</h1>

<fieldset>
	<legend>Supprimer un auteur</legend>
	<form method="post" action="">
		<p>Êtes-vous sûr de vouloir supprimer l'auteur <strong><a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></a></strong> ?</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
