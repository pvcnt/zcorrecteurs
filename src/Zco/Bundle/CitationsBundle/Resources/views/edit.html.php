<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Modifier une citation</h1>

<?php echo $view->render('ZcoCitationsBundle::form.html.php', ['form' => $form]) ?>