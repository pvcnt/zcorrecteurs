<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Ajouter un groupe</h1>

<?php echo $view->render('ZcoGroupesBundle::form.html.php', ['form' => $form]) ?>