<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Ajouter une annonce</h1>

<?php echo $view->render('ZcoAnnoncesBundle::_form.html.php', compact('pays', 'groupes', 'domaines', 'categories', 'annonce', 'attrPays', 'attrCategories', 'attrGroupes', 'attrDomaines')) ?>
