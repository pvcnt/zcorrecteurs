<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?> <small>Modifier le groupe</small></h1>

<?php echo $view->render('ZcoGroupesBundle::form.html.php', ['form' => $form]) ?>