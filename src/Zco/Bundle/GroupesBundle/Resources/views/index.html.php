<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div class="float-right">
    <a href="<?php echo $view['router']->path('zco_groups_new') ?>" class="btn btn-primary">
        <i class="icon-plus-sign icon-white"></i>
        Ajouter un groupe
    </a>
</div>

<h1>Groupes</h1>

<?php echo $view->render('ZcoGroupesBundle::groupsTable.html.php', ['ListerGroupes' => $ListerGroupes]) ?>

<h2>Liste des groupes secondaires</h2>

<?php echo $view->render('ZcoGroupesBundle::groupsTable.html.php', ['ListerGroupes' => $ListerGroupesSecondaires]) ?>