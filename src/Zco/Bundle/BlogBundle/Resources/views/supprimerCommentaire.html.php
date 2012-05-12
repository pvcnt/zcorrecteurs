<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></h1>

<?php if(!empty($InfosBillet['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?></h2>
<?php } ?>

<fieldset>
	<legend>Supprimer un commentaire</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir vraiment supprimer ce commentaire de
			<strong><a href="/membres/profil-<?php echo $InfosCommentaire['utilisateur_id']; ?>-<?php echo rewrite($InfosCommentaire['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosCommentaire['utilisateur_pseudo']); ?></a></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
