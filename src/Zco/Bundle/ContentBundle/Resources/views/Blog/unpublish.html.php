<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Dévalider un billet</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir dévalider ce billet ayant pour titre <strong><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></strong> ?
    </p>

    <p class="form-actions center">
        <input class="btn btn-primary" type="submit" value="Oui" />
        <a class="btn" href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>">Non</a>
    </p>
</form>
