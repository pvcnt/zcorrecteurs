<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Suppression d'un message du sujet</h1>

<form method="post" action="">
    <p>Êtes-vous sûr de vouloir supprimer ce message ? La suppression est irréversible.</p>

    <p class="form-actions center">
        <input type="submit" class="btn btn-primary" value="Oui"/>
        <a href="<?php echo $url ?>" class="btn">Non</a>
    </p>
</form>