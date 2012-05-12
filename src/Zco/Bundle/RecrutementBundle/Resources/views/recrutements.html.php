<?php $view->extend('::layouts/default.html.php') ?>

<h1>Espace recrutement</h1>

<p>
	Bienvenue dans l'espace recrutement ! Cet espace est ouvert lorsqu'un
	recrutement est en cours. C'est alors le moment pour vous de tenter votre
	chance si vous souhaitez intégrer notre équipe. Il vous suffit de sélectionner
	le recrutement vous intéressant, de lire les instructions, puis de postuler.
	Notez que vous pouvez retrouver les anciens recrutements également sur cette
	page.
</p>

<p>
	Un recrutement marqué <span class="gras rouge">en rouge</span> indique que
	vous participez mais avez une action à faire.<br />
	Un recrutement marqué <span class="gras vertf">en vert</span> indique que
	vous y participez et avez accompli ce qui était demandé.
</p>

<?php if(!empty($ListerRecrutements)){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 30%;">Nom</th>
			<th style="width: 15%;">Début</th>
			<th style="width: 25%;">Fin de dépôt des candidatures</th>
			<th style="width: 20%;">Groupe concerné</th>
			<?php if(verifier('recrutements_postuler')){ ?>
			<th style="width: 10%;">Postuler !</th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php
		$r_etats = array(RECRUTEMENT_FINI => 'terminés', RECRUTEMENT_OUVERT => 'en cours');
		$etat = 0;
		$nb = 0;
		foreach($ListerRecrutements as $r){
			if($etat != $r['recrutement_etat'])
			{
				$etat = $r['recrutement_etat'];
				echo '<tr class="grosse_cat"><td colspan="5">Recrutements '.$r_etats[$r['recrutement_etat']].'</td></tr>';
			}
			if($nb == 0 && $r['recrutement_etat'] != RECRUTEMENT_OUVERT)
			{
			?>
			<p>Aucun recrutement n'est actuellement en cours. Vous serez normalement informés de la prochaine phase par un billet sur <a href="/blog/">le blog</a>.</p>
			<?php
			}
			?>
			<tr>
				<td>
					<a href="recrutement-<?php echo $r['recrutement_id']; ?>-<?php echo rewrite($r['recrutement_nom']); ?>.html"<?php
		if(!empty($r['candidature_etat'])) {
			echo ' class="gras';
			if(in_array($r['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_REDACTION))) echo ' rouge"';
			elseif(in_array($r['candidature_etat'], array(CANDIDATURE_ENVOYE, CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE))) echo ' vertf"';
			else echo '"';
		}
?>>
					<?php echo htmlspecialchars($r['recrutement_nom']); ?></a>
					<?php if($r['recrutement_prive']) echo ' (<em>privé</em>)'; ?>
				</td>
				<td class="centre">
					<?php echo dateformat($r['recrutement_date']); ?>
				</td>
				<td class="centre">
					<?php echo dateformat($r['recrutement_date_fin_depot']); ?>
				</td>
				<td style="color: <?php echo $r['groupe_class']; ?>;">
					<?php echo htmlspecialchars($r['groupe_nom']);
					if($r['recrutement_nb_personnes'] > 0) echo ' ('.$r['recrutement_nb_personnes'].')'; ?>
				</td>
				<?php if(verifier('recrutements_postuler')){ ?>
				<td class="centre">
					<?php if($r['depot_possible'] || !empty($r['candidature_etat'])){ ?>
					<a href="postuler-<?php echo $r['recrutement_id']; ?>.html"><img src="/img/recrutement/postuler.png" alt="Postuler" /></a>
					<?php } else echo '-'; ?>
				</td>
				<?php } ?>
			</tr>
			<?php
			$nb ++;
		}
		?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun recrutement n'a encore été effectué.</p>
<?php } ?>
