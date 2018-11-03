<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Groupes</h1>

<div class="right my16">
    <a href="<?php echo $view['router']->path('zco_groups_new') ?>" class="btn btn-primary">
        Ajouter un groupe
    </a>
</div>

<?php echo $view->render('ZcoGroupesBundle::groupsTable.html.php', ['ListerGroupes' => $ListerGroupes]) ?>

<h2>Liste des groupes secondaires</h2>

<?php echo $view->render('ZcoGroupesBundle::groupsTable.html.php', ['ListerGroupes' => $ListerGroupesSecondaires]) ?>