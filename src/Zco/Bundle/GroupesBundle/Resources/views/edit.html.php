<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?></h1>

<?php echo $view->render('ZcoGroupesBundle::form.html.php', ['form' => $form]) ?>