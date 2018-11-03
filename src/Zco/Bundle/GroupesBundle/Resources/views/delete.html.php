<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?>  <small>Supprimer le groupe</small></h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer le groupe
        <strong><?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?></strong> ?
        Les <?php echo $InfosGroupe['groupe_effectifs']; ?> membres appartenant à ce groupe
        seront affectés au groupe par défaut.
    </p>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Oui"/>
        <a href="<?php echo $view['router']->path('zco_groups_index') ?>" class="btn">Non</a>
    </div>
</form>