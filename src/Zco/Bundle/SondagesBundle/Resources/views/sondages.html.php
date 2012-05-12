<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des sondages</h1>

<p>
	Voici la liste des sondages publiés sur ce site. Ils servent à l'équipe
	d'administration à vous consulter sur divers sujets, concernant vos habitudes
	ou votre opinion sur un point concernant l'évolution du site. Vous n'êtes en
	aucun cas obligé de répondre, mais vous pouvez de toute façon toujours voter
	blanc !
</p>

<?php if(!empty($ListerSondages)){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 30%;">Nom du sondage</th>
			<th style="width: 20%;">Créateur</th>
			<th style="width: 10%;">Questions</th>
			<th style="width: 20%;">Début</th>
			<th style="width: 20%;">Fin</th>
			<th style="width: 20%;">Votes</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($ListerSondages as $s){ ?>
		<tr>
			<td><a href="sondage-<?php echo $s['sondage_id']; ?>-<?php echo rewrite($s['sondage_nom']); ?>.html"><?php if(!$s['vote_possible']) echo ' <img src="/bundles/zcosondages/img/ferme.png" alt="[Fermé]" /> '; echo htmlspecialchars($s['sondage_nom']); ?></a></td>
			<td><a href="/membres/profil-<?php echo $s['utilisateur_id']; ?>-<?php echo rewrite($s['utilisateur_pseudo']); ?>.html" style="color: <?php echo $s['groupe_class']; ?>;"><?php echo htmlspecialchars($s['utilisateur_pseudo']); ?></a></td>
			<td class="centre"><?php echo $s['sondage_nb_questions']; ?></td>
			<td class="centre"><?php echo dateformat($s['sondage_date_debut']); ?></td>
			<td class="centre"><?php echo dateformat($s['sondage_date_fin']); ?></td>
			<td class="centre"><?php echo $s['sondage_nb_votes']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Il n'y a encore aucun sondage.</p>
<?php } ?>