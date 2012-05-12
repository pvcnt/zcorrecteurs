<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gestion des sujets d'aide</h1>

<?php if (verifier('aide_ajouter')){ ?>
<div class="UI_box gras centre"><a href="ajouter.html">Ajouter un nouveau sujet d'aide</a></div>
<?php } ?>

<table class="UI_items">
	<thead>
		<tr>
			<th>Titre de la page d'aide</th>
			<th>Dernière modification</th>
			<?php if (verifier('aide_modifier')){ ?>
			<th>Modifier</th>
			<?php } if (verifier('aide_supprimer')){ ?>
			<th>Supprimer</th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($categories as $cat){ ?>
		<tr class="bigcat">
			<td colspan="4"><?php echo htmlspecialchars($cat['nom']) ?></td>
		</tr>

		<?php foreach ($cat->Aide as $aide){ ?>
		<tr>
			<td><a href="page-<?php echo $aide['id'] ?>-<?php echo rewrite($aide['titre']) ?>.html">
				<?php echo htmlspecialchars($aide['titre']) ?>
			</a></td>
			<td class="centre"><?php echo dateformat($aide['date_edition']) ?></td>
			<?php if (verifier('aide_modifier')){ ?>
			<td class="centre"><a href="modifier-<?php echo $aide['id'] ?>.html">
				<img src="/img/editer.png" alt="Modifier le contenu de la page" />
			</a></td>
			<?php } if (verifier('aide_supprimer')){ ?>
			<td class="centre"><a href="supprimer-<?php echo $aide['id'] ?>.html">
				<img src="/img/supprimer.png" alt="Supprimer la page" />
			</a></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>