<?php $view->extend('::layouts/default.html.php') ?>

<h1>Catégories</h1>

<p class="gras centre">
    <a href="ajouter.html">Ajouter une catégorie</a> –
    <a href="image.html">Graphique des catégories</a>
</p>

<table class="UI_items" onmouseover="InverserEtat(event);" onmouseout="InverserEtat(event);">
	<thead>
		<tr>
			<th style="width: 60%;">Nom</th>
			<th style="width: 10%;">Monter</th>
			<th style="width: 10%;">Descendre</th>
			<th style="width: 10%;">Éditer</th>
			<th style="width: 10%;">Supprimer</th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach($categories as $c)
		{
			$marqueur = '';
			for($i = 0 ; $i < $c['cat_niveau'] ; $i++)
			{
				$marqueur .= '.....';
			}
		?>
		<tr>
			<td><?php echo $marqueur; ?> <?php echo htmlspecialchars($c['cat_nom']); ?></td>
			<td class="centre">
				<a href="?monter=<?php echo $c['cat_id']; ?>"><img src="/img/misc/monter.png" alt="Monter" /></a>
			</td>
			<td class="centre">
				<a href="?descendre=<?php echo $c['cat_id']; ?>"><img src="/img/misc/descendre.png" alt="Descendre" /></a>
			</td>
			<td class="centre">
				<a href="editer-<?php echo $c['cat_id']; ?>.html"><img src="/img/editer.png" alt="Éditer" /></a>
			</td>
			<td class="centre">
				<?php if($c['cat_droite'] - $c['cat_gauche'] == 1){ ?>
				<a href="supprimer-<?php echo $c['cat_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
