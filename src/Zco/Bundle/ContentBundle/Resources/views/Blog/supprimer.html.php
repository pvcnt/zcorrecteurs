<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer un billet</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer ce billet de <strong><?php echo htmlspecialchars($InfosBillet['utilisateur_pseudo']); ?></strong>
        ayant pour titre <strong><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></strong> ?
    </p>

    <p class="form-actions center">
        <input type="submit" name="confirmer" value="Oui" />
        <a class="btn" href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>">Non</a>
    </p>
</form>
