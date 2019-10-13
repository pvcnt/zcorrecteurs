<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Modifier une dictée</h1>

<?php echo $view->render('ZcoDicteesBundle::form.html.php', ['form' => $form]) ?>