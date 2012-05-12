<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des dons</h1>

<?php if (verifier('dons_ajouter')){ ?>
<div class="UI_box gras centre">
	<a href="ajouter.html">Ajouter un don</a>
</div>
<?php } ?>

<table class="UI_items" style="max-width: 400px;">
	<thead>
		<tr>
			<th style="width: 50%;">Membre</th>
			<th style="width: 30%;">Date</th>
			<th style="width: 20%;">Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php if (count($dons) > 0){ ?>
		<?php foreach ($dons as $i => $don){ ?>
		<tr<?php if ($i % 2) echo ' class="odd"' ?>>
			<td>
			    <?php if (!empty($don['utilisateur_id'])){ ?>
			    <?php echo $don->Utilisateur ?> <?php if (!empty($don['nom'])) echo ' | ' ?>
			    <?php } if (!empty($don['nom'])){ ?>
			    <?php echo htmlspecialchars($don['nom']) ?>
			    <?php } ?>
			</td>
			<td class="centre"><?php echo dateformat($don['date'], DATE) ?></td>
			<td class="centre">
				<?php if (verifier('dons_editer')){ ?>
				<a href="modifier-<?php echo $don['id'] ?>.html">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
				<?php } if (verifier('dons_supprimer')){ ?>
				<a href="?supprimer=<?php echo $don['id'] ?>&token=<?php echo $_SESSION['token'] ?>" onclick="if(confirm('Voulez-vous vraiment supprimer ce don ?')) document.location=this.href; return false;">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		<?php } else{ ?>
		<tr><td colspan="3" class="centre">Aucun don n'a été trouvé.</td></tr>
		<?php } ?>
	</tbody>
</table>
