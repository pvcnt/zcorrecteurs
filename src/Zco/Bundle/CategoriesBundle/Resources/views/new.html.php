<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Créer une catégorie</h1>

<?php echo $view->render('ZcoCategoriesBundle::form.html.php', ['form' => $form]) ?>