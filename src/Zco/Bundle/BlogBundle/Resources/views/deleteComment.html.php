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
			<strong><a href="<?php echo $view['router']->path('zco_user_profile', ['id' => $InfosCommentaire['utilisateur_id'], 'slug' => rewrite($InfosCommentaire['utilisateur_pseudo'])]) ?>"><?php echo htmlspecialchars($InfosCommentaire['utilisateur_pseudo']); ?></a></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
            <a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>">Non</a>
		</p>
	</form>
</fieldset>
