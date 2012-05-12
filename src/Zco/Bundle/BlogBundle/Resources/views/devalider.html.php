<?php $view->extend('::layouts/default.html.php') ?>

<h1>Dévalider un billet</h1>

<fieldset>
	<legend>Dévalider un billet</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir dévalider ce billet ayant pour titre <strong><a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>.html"><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></a></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
