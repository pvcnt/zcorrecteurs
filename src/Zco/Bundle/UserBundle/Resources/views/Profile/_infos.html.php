<div class="accordion">
	<?php $c = 0; if (verifier('membres_voir_ch_pseudos') && count($newPseudo)): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#profile-infos-pseudos">
                <?php echo count($newPseudo) ?> changement<?php echo pluriel(count($newPseudo)) ?> de pseudo
            </a>
        </div>
        <div id="profile-infos-pseudos" class="accordion-body collapse">
            <div class="accordion-inner">
				<?php $ch_etats = array(CH_PSEUDO_ACCEPTE => '<span class="vertf">Accepté</span>', CH_PSEUDO_ATTENTE => 'En attente', CH_PSEUDO_AUTO => 'Automatique', CH_PSEUDO_REFUSE => '<span class="rouge">Refusé</span>'); ?>
				<table class="table">
					<thead>
						<tr>
							<th style="width: 9%;">Ancien pseudo</th>
							<th style="width: 10%;">Nouveau pseudo</th>
							<th style="width: 8%;">Admin</th>
							<th style="width: 10%;">Date</th>
							<th style="width: 10%;">Date réponse</th>
							<th style="width: 5%;">État</th>
							<th style="width: 25%;">Raison</th>
							<th style="width: 25%;">Réponse</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($newPseudo as $query): ?>
						<tr>
							<td><?php echo htmlspecialchars($query->getOldUsername()) ?></td>
							<td><?php echo htmlspecialchars($query->getNewUsername()) ?></td>
							<td>
								<?php if ($query->getStatus() != CH_PSEUDO_ATTENTE): ?>
								<?php if ($query->getAdmin()): ?>
									<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $query->getAdminId(), 'slug' => rewrite($query->getAdmin()->getUsername()))) ?>">
										<?php echo htmlspecialchars($query->getAdmin()->getUsername()) ?>
									</a>
								<?php else: ?>
									Anonyme
								<?php endif ?>
								<?php else: ?>
								-
								<?php endif ?>
							</td>
							<td class="center"><?php echo dateformat($query->getDate(), DATE); ?></td>
							<td class="center">
								<?php if (in_array($query->getStatus(), array(CH_PSEUDO_ACCEPTE, CH_PSEUDO_REFUSE))): ?>
								 	<?php echo dateformat($query->getResponseDate(), DATE) ?>
								<?php else: ?>
								 	-
								<?php endif ?>
							</td>
							<td><?php echo $ch_etats[$query->getStatus()] ?></td>
							<td><?php echo $view['messages']->parse($query->getReason()) ?></td>
							<td><?php echo $view['messages']->parse($query->getAdminResponse()) ?></td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div> <!-- /.accordion-inner -->
		</div> <!-- /.accordion-body -->
	</div> <!-- /.accordion-group -->
	<?php ++$c; endif ?>

	<?php if (verifier('groupes_changer_membre') && count($ListerGroupes) > 0): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#profile-infos-groups">
                <?php echo count($ListerGroupes) ?> changement<?php echo pluriel(count($ListerGroupes)) ?> de groupe
            </a>
        </div>
        <div id="profile-infos-groups" class="accordion-body collapse">
            <div class="accordion-inner">
            	<table class="table">
					<thead>
						<tr>
							<th style="width:15%">Responsable du changement</th>
							<th>Date</th>
							<th>Ancien groupe</th>
							<th>Nouveau groupe</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($ListerGroupes as $ch): ?>
						<tr>
							<td class="center"><?php if(!empty($ch['utilisateur_id'])) { ?><a href="profil-<?php echo $ch['utilisateur_id'];?>-<?php echo rewrite($ch['pseudo_responsable']);?>.html"><?php echo htmlspecialchars($ch['pseudo_responsable']);?></a><?php } else { echo htmlspecialchars($ch['pseudo_responsable']); }?></td>
							<td class="center"><?php echo dateformat($ch['chg_date'], DATE); ?></td>
							<td class="center"><span style="color:<?php echo $ch['couleur_ancien_groupe']; ?>"><?php echo $ch['ancien_groupe']; ?></span></td>
							<td class="center"><span style="color:<?php echo $ch['couleur_nouveau_groupe']; ?>"><?php echo $ch['nouveau_groupe']; ?></span></td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div> <!-- /.accordion-inner -->
		</div> <!-- /.accordion-body -->
	</div> <!-- /.accordion-group -->
	<?php ++$c; endif ?>
	
	<?php if ($c === 0): ?>
		<div class="alert alert-info">
			Nous ne possédons aucune information supplémentaire sur <?php echo htmlspecialchars($user->getUsername()) ?>, désolé !
		</div>
	<?php endif ?>
</div> <!-- /.accordion -->