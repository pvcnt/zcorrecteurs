<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div class="float-right">
    <a href="<?php echo $view['router']->path('zco_quote_new') ?>" class="btn btn-primary">
        <i class="icon-plus-sign icon-white"></i>
        Ajouter une citation
    </a>
</div>

<h1>Gestion des citations</h1>

<p>
    Les citations sont de courtes phrases célèbres qui apparaissent dans l'en-tête du site à droite de la bannière.
    Elles sont en rotation automatique parmi celles qui ont été activées depuis cette interface.
    La citation affichée change toutes les heures.
</p>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <td colspan="5">
            Page : <?php echo implode('', $pages) ?>
        </td>
    </tr>
    <tr>
        <th>Auteur</th>
        <th>Contenu</th>
        <th>Active ?</th>
        <th>Modifier</th>
        <th>Supprimer</th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <td colspan="5">
            Page : <?php echo implode('', $pages) ?>
        </td>
    </tr>
    </tfoot>

    <tbody>
    <?php foreach ($quotes as $row): ?>
        <tr>
            <td>
                <?php echo htmlspecialchars($row['auteur_prenom']) ?>
                <?php echo htmlspecialchars($row['auteur_nom']) ?>
            </td>
            <td>
                <?php echo htmlspecialchars($row['contenu']) ?>
            </td>
            <td class="center">
                <img src="/bundles/zcocore/img/generator/boolean-<?php echo htmlspecialchars($row['statut']) ? 'yes' : 'no' ?>.png"
                     alt="<?php echo htmlspecialchars($row['statut']) ? 'Oui' : 'Non' ?>"/>
            </td>
            <td class="center">
                <a href="<?php echo $view['router']->path('zco_quote_edit', ['id' => $row['id']]) ?>">
                    <img src="/img/editer.png" alt="Modifier"/>
                </a>
            </td>
            <td class="center">
                <a href="<?php echo $view['router']->path('zco_quote_delete', ['id' => $row['id']]) ?>">
                    <img src="/img/supprimer.png" alt="Supprimer"/>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td class="bold center" colspan="5">
            <?php echo $totalCount ?> citation<?php echo pluriel($totalCount) ?>
        </td>
    </tr>
    </tbody>
</table>