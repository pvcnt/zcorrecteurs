<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer une citation</h1>

<form method="post" action="">
    <p>
        Voulez-vous vraiment supprimer cette citation de
        <strong><?php echo htmlspecialchars($quote['auteur_prenom']) ?> <?php echo htmlspecialchars($quote['auteur_nom']) ?></strong>
        ?
    </p>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a href="<?php echo $view['router']->path('zco_quote_index') ?>" class="btn">Non</a>
    </div>
</form>