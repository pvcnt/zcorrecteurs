<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer un quiz</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer ce quiz intitulé
        <strong><?php echo htmlspecialchars($quiz['nom']); ?></strong> ?
        Toutes les questions contenues dedans seront également supprimées, ainsi que les scores associés.
    </p>
    <p class="form-actions">
        <input type="submit" class="btn btn-primary" value="Oui" name="confirmer"/>
        <a class="btn" href="<?php echo $view['router']->path('zco_quiz_admin') ?>">Non</a>
    </p>
</form>