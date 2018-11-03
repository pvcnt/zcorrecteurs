<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosCategorie['cat_nom']) ?>  <small>Supprimer la catégorie</small></h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer la catégorie <strong><?php echo htmlspecialchars($InfosCategorie['cat_nom']); ?></strong> ?
    </p>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Oui"/>
        <a href="<?php echo $view['router']->path('zco_categories_index') ?>" class="btn">Non</a>
    </div>
</form>