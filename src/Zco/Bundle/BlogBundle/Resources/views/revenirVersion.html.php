<?php $view->extend('::layouts/default.html.php') ?>

<h1>Revenir à une version antérieure</h1>

<fieldset>
	<legend>Revenir à une version antérieure</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir revenir à la version n<sup>o</sup>&nbsp;<?php echo $id_version; ?>
			du billet intitulé
			<strong><a href="billet-<?php echo $InfosBillet['blog_id']; ?>.html"><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></a></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>