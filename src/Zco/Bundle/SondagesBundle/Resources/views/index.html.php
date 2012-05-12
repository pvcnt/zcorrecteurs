<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des sondages</h1>

<p>
	Voici la liste des sondages publiés sur ce site. Ils servent à l'équipe
	d'administration à vous consulter sur divers sujets, concernant vos habitudes
	ou votre opinion sur un point concernant l'évolution du site. Vous n'êtes en
	aucun cas obligé de répondre, mais vous pouvez de toute façon toujours voter
	blanc !
</p>

<?php if (count($sondages) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 30%;">Nom du sondage</th>
			<th style="width: 15%;">Créateur</th>
			<th style="width: 10%;">Questions</th>
			<th style="width: 15%;">Début</th>
			<th style="width: 15%;">Fin</th>
			<th style="width: 10%;">Votes</th>
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
			<td class="centre"><?php echo $sondage['nb_votes'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Il n'y a encore aucun sondage.</p>
<?php } ?>

<?php if (verifier('sondages_ajouter') || verifier('sondages_editer') || verifier('sondages_supprimer') || verifier('sondages_editer_siens') || verifier('sondages_supprimer_siens')){ ?>
<div class="UI_box gras centre" style="margin-top: 20px;">
	<?php if (verifier('sondages_ajouter')){ ?>
	<a href="ajouter.html">Créer un nouveau sondage</a><br />
	<?php } ?>
	<a href="gestion.html">Accéder à la gestion des sondages</a>
</div>
<?php } ?>