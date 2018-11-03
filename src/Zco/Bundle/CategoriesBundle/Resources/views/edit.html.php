<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosCategorie['cat_nom']) ?> <small>Modifier la catégorie</small></h1>

<?php echo $view->render('ZcoCategoriesBundle::form.html.php', ['form' => $form]) ?>