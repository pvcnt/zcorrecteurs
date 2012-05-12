<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des candidatures de <?php echo htmlspecialchars($Membre['pseudo']) ?></h1>

<p>
	Ci-dessous sont listées l'ensemble des candidatures envoyées par <a href="/membres/profil-<?php echo $Membre['id'] ?>-<?php echo rewrite($Membre['pseudo']) ?>.html"><?php echo htmlspecialchars($Membre['pseudo']) ?></a>.<br />
	Actuellement, ce membre a participé à <b><?php echo count($Candidatures->toArray()) ?> recrutement<?php echo pluriel(count($Candidatures->toArray())); ?></b>.
	<br /><br />
</p>

<?php
$c_etats = array(CANDIDATURE_ENVOYE => 'En attente',
	CANDIDATURE_REDACTION => 'En rédaction',
	CANDIDATURE_ACCEPTE => '<span class="vertf">Accepté</span>',
	CANDIDATURE_ATTENTE_TEST => 'En test',
	CANDIDATURE_TESTE=> 'Testé',
	CANDIDATURE_REFUSE => '<span class="rouge">Refusé</span>',
	CANDIDATURE_DESISTE => '<span class="rouge">Désistée</span>'); ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 30%;">Nom du recrutement</th>
			<th style="width: 25%;">Date de début</th>
			<th style="width: 15%;">État de la candidature</th>
			<th style="width: 20%;">Groupe concerné</th>
			<th style="width: 20%;">Action</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$etat = 0;
		$nb = 0;
		$r_etats = array(RECRUTEMENT_FINI => 'terminés', RECRUTEMENT_OUVERT => 'en cours');
		foreach($Candidatures->toArray() as $c){
			if($etat != $c['Recrutement']['etat'])
			{
				$etat = $c['Recrutement']['etat'];
				echo '<tr class="bigcat"><td colspan="5">Recrutements '.$r_etats[$c['Recrutement']['etat']].'</td></tr>';
			}
			?>
			<tr>
				<td>
					<a href="recrutement-<?php echo $c['Recrutement']['id']; ?>-<?php echo rewrite($c['Recrutement']['nom']); ?>.html">
					<?php echo htmlspecialchars($c['Recrutement']['nom']); ?></a>
				</td>
				<td class="centre">
					<?php echo dateformat($c['Recrutement']['date']); ?>
				</td>
				<td class="centre">
					<?php echo $c_etats[$c['etat']]; ?>

				</td>
				<td class="centre" style="color: <?php echo $c['Recrutement']['Groupe']['class']; ?>;">
					<?php echo htmlspecialchars($c['Recrutement']['Groupe']['nom']); ?>
				</td>
				<td class="centre">
					<a href="candidature-<?php echo $c['id']; ?>.html"><img src="/img/recrutement/voir.png" alt="Voir la candidature" title="Voir la candidature" /></a>
				</td>
			</tr>
			<?php
			$nb++;
		}
		if($nb == 0)
		{
		?>
			<tr>
				<td colspan="5" class="centre">	
					Ce membre n'a participé à aucun recrutement.
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>