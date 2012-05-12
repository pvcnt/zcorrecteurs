<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un recrutement</h1>

<?php echo $view->render('ZcoRecrutementBundle::_formRecrutement.html.php', array('form' => $form)) ?>
