<?php $view->extend('ZcoFileBundle::layout.html.php') ?>

<h1>Rechercher sur Wikimédia Commons</h1>

<form class="form-search" action="" method="post">
    <div class="row">
        <div class="btn-group span2">
            <a class="btn active" href="#" id="thumb_view_link"><i class="icon-th-large"></i></a>
            <a class="btn" href="#" id="list_view_link"><i class="icon-list"></i></a>
        </div>
        <div class="span4">
            <input type="text" name="search" id="search" class="search-query" placeholder="Rechercher sur Commons…" />
        </div>
    </div>
</form>

<div id="thumbnails-loader">
    <img src="/img/ajax-loader.gif" alt="Chargement…" />
    <em>Chargement en cours…</em>
</div>
<ul id="thumbnails" class="thumbnails"></ul>

<table class="table table-striped table-bordered" id="thumbnails-table" style="display: none;">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Poids</th>
            <th>Création</th>
            <th>Actions</th>
        </tr>
    </thead>
    
    <tbody>
    </tbody>
</table>

<?php $view['javelin']->initBehavior('zco-files-switch-view') ?>
<?php $view['javelin']->initBehavior('zco-files-load-files-async', array(
    'folder' => 'commons',
)) ?>
