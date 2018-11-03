<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Créer une catégorie</h1>

<?php echo $view->render('ZcoContentBundle:Category:form.html.php', ['form' => $form]) ?>