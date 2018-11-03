<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div class="float-right">
    <a href="<?php echo $view['router']->path('zco_categories_new') ?>" class="btn btn-primary">
        <i class="icon-plus-sign icon-white"></i>
        Ajouter une catégorie
    </a>
</div>

<h1>Catégories</h1>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Monter</th>
			<th>Descendre</th>
			<th>Éditer</th>
			<th>Supprimer</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($categories as $c) { ?>
		<tr>
			<td>
                <?php echo str_repeat('...', $c['cat_niveau']) ?>
                <?php echo htmlspecialchars($c['cat_nom']); ?>
            </td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_categories_index', ['up' => $c['cat_id']]) ?>"><img src="/img/misc/monter.png" alt="Monter" /></a>
			</td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_categories_index', ['down' => $c['cat_id']]) ?>"><img src="/img/misc/descendre.png" alt="Descendre" /></a>
			</td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_categories_edit', ['id' => $c['cat_id']]) ?>"><img src="/img/editer.png" alt="Éditer" /></a>
			</td>
			<td class="center">
				<?php if($c['cat_droite'] - $c['cat_gauche'] == 1){ ?>
				<a href="<?php echo $view['router']->path('zco_categories_delete', ['id' => $c['cat_id']]) ?>"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
