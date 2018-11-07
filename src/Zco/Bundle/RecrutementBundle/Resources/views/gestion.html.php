<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des recrutements</h1>

<p class="gras centre"><a href="ajouter.html">Ajouter un recrutement</a></p>

<p>
	Sur cette page se trouvent tous les recrutements, y compris les recrutements
	cachés. Ceux-ci désignent des recrutements invisibles, en cours de
	préparation par exemple. Ils sont visibles uniquement ici.
</p>

<?php if(!empty($ListerRecrutements)){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 20%;">Nom</th>
			<th style="width: 10%;">Nb candidatures</th>
			<th style="width: 15%;">Début</th>
			<th style="width: 15%;">Fin de dépôt des candidatures</th>
			<th style="width: 15%;">Groupe concerné</th>
			<th style="width: 5%;">Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$r_etats = array(RECRUTEMENT_CACHE => 'en préparation (cachés)',
			RECRUTEMENT_FINI => 'terminés',
			RECRUTEMENT_OUVERT => 'en cours');
		$etat = 0;
		foreach($ListerRecrutements as $r){
			if($etat != $r['recrutement_etat'])
			{
				$etat = $r['recrutement_etat'];
				echo '<tr class="bigcat"><td colspan="8">Recrutements '.$r_etats[$r['recrutement_etat']].'</td></tr>';
			}
			?>
			<tr>
				<td>
					<a href="recrutement-<?php echo $r['recrutement_id']; ?>-<?php echo rewrite($r['recrutement_nom']); ?>.html">
						<?php echo htmlspecialchars($r['recrutement_nom']); ?>
					</a>
				</td>
				<td class="centre">
					<a href="recrutement-<?php echo $r['recrutement_id']; ?>-<?php echo rewrite($r['recrutement_nom']); ?>.html#candidatures">
						<?php echo $r['nb_candidatures']; ?>
						<img src="/img/recrutement/voir.png" alt="" />
					</a>
				</td>
				<td class="centre">
					<?php echo dateformat($r['recrutement_date']); ?>
				</td>
				<td class="centre">
					<?php echo dateformat($r['recrutement_date_fin_depot']); ?>
				</td>
				<td>
					<?php echo htmlspecialchars($r['groupe_nom']);
					if($r['recrutement_nb_personnes'] > 0) echo ' ('.$r['recrutement_nb_personnes'].')'; ?>
				</td>
				<td class="centre">
					<a href="editer-recrutement-<?php echo $r['recrutement_id']; ?>.html">
						<img src="/img/editer.png" alt="Modifier" />
					</a>
					<a href="supprimer-recrutement-<?php echo $r['recrutement_id']; ?>.html">
						<img src="/img/supprimer.png" alt="Supprimer" />
					</a>
				</td>
			</tr>
			<?php }	?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun recrutement n'a encore été effectué.</p>
<?php } ?>
