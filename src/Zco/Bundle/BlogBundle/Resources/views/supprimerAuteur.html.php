<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer un auteur</h1>

<form method="post" action="">
    <p>Êtes-vous sûr de vouloir supprimer l'auteur <strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong> ?</p>

    <p class="form-actions center">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a class="btn" href="<?php echo $view['router']->path('zco_blog_manage', ['id' => $InfosBillet['blog_id']]) ?>">Non</a>
    </p>
</form>
