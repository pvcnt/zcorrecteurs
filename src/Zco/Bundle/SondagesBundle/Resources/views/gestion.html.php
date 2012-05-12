<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gestion des sondages</h1>

<?php if (verifier('sondages_ajouter')){ ?>
<div class="UI_box gras centre"><a href="ajouter.html">Créer un nouveau sondage</a></div>
<?php } ?>

<?php if (count($sondages) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 30%;">Nom du sondage</th>
			<th style="width: 15%;">Créateur</th>
			<th style="width: 5%;">Questions</th>
			<th style="width: 15%;">Début</th>
			<th style="width: 15%;">Fin</th>
			<?php if (verifier('sondages_editer') || verifier('sondages_editer_siens')){ ?>
			<th style="width: 10%;">État</th>
			<th style="width: 5%;">Modifier</th>
			<?php } if(verifier('sondages_supprimer') || verifier('sondages_supprimer_siens')){ ?>
			<th style="width: 5%;">Supprimer</th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($sondages as $i => $sondage){ ?>
		<tr class="<?php echo $i % 2 ? 'odd' : 'even' ?>">
			<td>
				<a href="sondage-<?php echo $sondage['id'] ?>-<?php echo rewrite($sondage['nom']); ?>.html">
					<?php if (!$sondage->estOuvert()){ ?>
					<img src="/bundles/zcoforum/img/cadenas.png" alt="[Fermé]" />
					<?php } ?>
					<?php echo htmlspecialchars($sondage['nom']) ?>
				</a>
			</td>
			<td>
				<?php echo $sondage->Utilisateur ?>
			</td>
			<td class="centre"><?php echo $sondage['nb_questions'] ?></td>
			<td class="centre"><?php echo dateformat($sondage['date_debut']) ?></td>
			<td class="centre"><?php echo dateformat($sondage['date_fin']) ?></td>
			<?php if (verifier('sondages_editer') || verifier('sondages_editer_siens')){ ?>
			<td class="centre"><?php echo $sondage['ouvert'] ? '<span class="vertf">Visible</span>' : '<span class="rouge">Masqué</span>' ?></td>
			<td class="centre">
				<?php if ($sondage['utilisateur_id'] == $_SESSION['id'] || verifier('sondages_editer')){ ?>
				<a href="modifier-<?php echo $sondage['id']; ?>.html">
					<img src="/img/editer.png" alt="Modifier le sondage" />
				</a>
				<?php } else echo '-' ?>
			</td>
			<?php } if(verifier('sondages_supprimer') || verifier('sondages_supprimer_siens')){ ?>
			<td class="centre">
				<?php if ($sondage['utilisateur_id'] == $_SESSION['id'] || verifier('sondages_supprimer')){ ?>
				<a href="supprimer-<?php echo $sondage['id']; ?>.html">
					<img src="/img/supprimer.png" alt="Supprimer le sondage" />
				</a>
				<?php } else echo '-'; ?>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Il n'y a encore aucun sondage.</p>
<?php } ?>