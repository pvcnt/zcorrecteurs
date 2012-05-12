<?php $view->extend('ZcoFileBundle::layout.html.php') ?>

<h1><?php echo htmlspecialchars($currentFolder['name']) ?></h1>

<form class="form-search">
    <div class="row-fluid">
        <div class="btn-group span2">
            <a class="btn active" href="#" id="thumb_view_link"><i class="icon-th-large"></i></a>
            <a class="btn" href="#" id="list_view_link"><i class="icon-list"></i></a>
        </div>
        <div class="span5">
            <input type="text" id="search" class="search-query" placeholder="Rechercher des fichiers…" />
        </div>
        <div class="span3">
            <input type="button" class="btn" id="delete_button" value="Supprimer les fichiers sélectionnés" />
        </div>
    </div>
</form>
<div class="cleaner">&nbsp;</div>

<div class="progress progress-<?php echo $usageClass ?>" id="folder-usage-progress"<?php if ($quota <= -1) echo ' style="display: none;"' ?>>
	<div class="bar" style="width: <?php echo $ratio > 5 ? $ratio : 5 ?>%;"><?php echo $ratio ?> %</div>
</div>

<div id="thumbnails-loader">
    <img src="/img/ajax-loader.gif" alt="Chargement…" />
    <em>Chargement en cours…</em>
</div>
<ul id="thumbnails" class="thumbnails" style="display: none;"></ul>

<table class="table table-striped table-bordered" id="thumbnails-table" style="display: none;">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Poids</th>
            <th>Création</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="modal fade" id="batch-delete-confirmation-modal" style="display: none;">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Confirmation</h3>
    </div>
    <div class="modal-body">
        <p>
            Vous vous apprêtez à supprimer plusieurs fichiers. Si vous 
            confirmez, ceux-ci seront définitivement perdus et tous les endroits 
            où ils étaient affichés ne seront plus en mesure de le faire.
            Est-ce vraiment ce que vous souhaitez ?
        </p>
    </div>
    <div class="modal-footer">
        <a href="#" id="batch-delete-confirmation-modal-cancel" class="btn">Annuler</a>
        <a href="#" id="batch-delete-confirmation-modal-confirm" class="btn btn-primary">Supprimer</a>
    </div>
</div>

<div class="modal fade" id="delete-confirmation-modal" style="display: none;">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Confirmation</h3>
    </div>
    <div class="modal-body">
        <p>
            Voulez-vous vraiment supprimer ce fichier ?
        </p>
    </div>
    <div class="modal-footer">
        <a href="#" id="delete-confirmation-modal-cancel" class="btn">Annuler</a>
        <a href="#" id="delete-confirmation-modal-confirm" class="btn btn-primary">Supprimer</a>
    </div>
</div>

<?php $view['javelin']->initBehavior('zco-files-load-files-async', array(
    'folder' => $currentFolder['id'],
    'urlExtraData' => array(
        'textarea' => $textarea,
        'input' => $input,
    ),
)) ?>
<?php $view['javelin']->initBehavior('zco-files-switch-view') ?>