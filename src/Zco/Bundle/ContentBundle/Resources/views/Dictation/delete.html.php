<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer une dictée</h1>

<form method="post" action="">
    <?php if($Dictee->etat == DICTEE_VALIDEE): ?>
    <div class="alert alert-warn">Attention, cette dictée est accessible publiquement à tous les visiteurs.</div>
    <?php endif; ?>
    <p>
        Êtes-vous sûr de vouloir supprimer cette dictée,
        dont le titre est
        <strong><?php echo htmlspecialchars($Dictee->titre) ?></strong> ?
    </p>

    <p class="form-actions center">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a href="<?php echo $url ?>" class="btn">Non</a>
    </p>
</form>