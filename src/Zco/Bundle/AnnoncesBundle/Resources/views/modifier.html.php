<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Modifier une annonce</h1>

<?php echo $view->render('ZcoAnnoncesBundle::_form.html.php', compact('annonce', 'attrPays', 'attrCategories', 'attrGroupes', 'attrDomaines', 'pays', 'groupes', 'domaines', 'categories')) ?>