<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer tous les commentaires</h1>

<fieldset>
	<legend>Supprimer tous les commentaires</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer <em>tous les commentaires</em> du billet <strong><a href="billet-<?php echo $InfosBillet['blog_id']; ?>.html"><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></a></strong> ?<br />
			Cela fait un total de <?php echo $InfosBillet['blog_nb_commentaires']; ?> commentaire<?php if($InfosBillet['blog_nb_commentaires'] > 1) echo 's'; ?> qui seront supprimés de façon irréversible.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
