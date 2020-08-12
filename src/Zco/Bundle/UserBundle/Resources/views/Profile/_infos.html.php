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
									<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $query->getAdminId(), 'slug' => rewrite($query->getAdmin()->getUsername()))) ?>">
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

	<?php if (verifier('membres_voir_avertos') && count($warnings) > 0): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#profile-infos-warnings">
                <?php echo $user->getPercentage() ?> % d'avertissement
            </a>
        </div>
        <div id="profile-infos-warnings" class="accordion-body collapse">
            <div class="accordion-inner">
            	<table class="table">
					<thead>
						<tr>
							<th style="width: 10%;">Admin</th>
							<th style="width: 15%;">Date</th>
							<th style="width: 10%;">Niveau</th>
							<th style="width: 5%;">Lien</th>
							<th style="width: 30%;">Raison admin</th>
							<th style="width: 30%;">Raison</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($warnings as $warning): ?>
						<tr>
							<td>
								<?php if ($warning->getAdmin()): ?>
								<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $warning->getAdminId(), 'slug' => rewrite($warning->getAdmin()->getUsername()))) ?>">
									<?php echo htmlspecialchars($warning->getAdmin()->getUsername()) ?>
								</a>
								<?php else: ?>
								Anonyme
								<?php endif ?>
							</td>
							<td class="center">
								<?php echo dateformat($warning->getDate(), DATE) ?>
							</td>
							<td class="center">
								<span class="<?php if ($warning->getPercentage() < 0) echo ' vertf'; elseif ($warning->getPercentage() > 0) echo 'rouge'; ?>">
									<?php echo $warning->getPercentage() ?> %
								</span>
							</td>
							<td class="center">
								<?php if ($warning->hasLink()): ?>
								<a href="<?php echo htmlspecialchars($warning->getLink()); ?>">Lien</a>
								<?php else: ?>
								-
								<?php endif ?>
							</td>
							<td><?php echo $view['messages']->parse($warning->getAdminReason()) ?></td>
							<td><?php echo $view['messages']->parse($warning->getReason()) ?></td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div> <!-- /.accordion-inner -->
		</div> <!-- /.accordion-body -->
	</div> <!-- /.accordion-group -->
	<?php ++$c; endif ?>

	<?php if (verifier('voir_sanctions') && count($punishments) > 0): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#profile-infos-punishments">
                <?php echo count($punishments) ?> sanction<?php echo pluriel(count($punishments)) ?>
            </a>
        </div>
        <div id="profile-infos-punishments" class="accordion-body collapse">
            <div class="accordion-inner">
            	<table class="table">
					<thead>
						<tr>
							<th style="width: 8%;">Admin</th>
							<th style="width: 8%;">Sanction</th>
							<th style="width: 10%;">Date</th>
							<th style="width: 5%;">Litige</th>
							<th style="width: 30%;">Raison admin</th>
							<th style="width: 30%;">Raison</th>
							<th style="width: 10%;">Durée</th>
							<?php if (verifier('sanctionner')): ?>
							<th style="width: 5%;">Arrêter</th>
							<?php endif ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($punishments as $punishment): ?>
						<tr>
							<td>
								<?php if ($punishment->getAdmin()): ?>
								<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $punishment->getAdminId(), 'slug' => rewrite($punishment->getAdmin()->getUsername()))) ?>">
									<?php echo htmlspecialchars($punishment->getAdmin()->getUsername()) ?>
								</a>
								<?php else: ?>
								Anonyme
								<?php endif ?>
							</td>
							<td><?php echo htmlspecialchars($punishment->getGroup()) ?></td>
							<td class="center">
								<?php echo dateformat($punishment->getDate(), DATE) ?>
							</td>
							<td class="center">
								<?php if ($punishment->hasLink()): ?>
								<a href="<?php echo htmlspecialchars($punishment->getLink()); ?>">Lien</a>
								<?php else: ?>
								-
								<?php endif ?>
							</td>
							<td><?php echo $view['messages']->parse($punishment->getAdminReason()) ?></td>
							<td><?php echo nl2br(htmlspecialchars($punishment->getReason())) ?></td>
							<td class="center">
								<?php if (!$punishment->isUnlimited()): ?>
									<?php echo $punishment->getDuration() ?> jour<?php echo pluriel($punishment->getDuration()) ?>
									<?php if (!$punishment->isFinished()): ?><br /> 
									(<em><?php echo $punishment->getRemainingDuration() ?> restant<?php echo pluriel($punishment->getRemainingDuration()) ?></em>)
									<?php endif ?>
								<?php else: ?>
									À vie
								<?php endif ?>
							</td>
							<?php if (verifier('sanctionner')): ?>
							<td class="center">
								<?php if (!$punishment->isFinished()): ?>
									<a href="<?php echo $view['router']->generate('zco_user_admin_cancelPunishment', array('id' => $punishment->getId())) ?>" title="Arrêter la sanction">
										<img src="/img/misc/delete.png" alt="Arrêter" />
									</a>
								<?php else: ?>
									Finie
								<?php endif ?>
							</td>
							<?php endif ?>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div> <!-- /.accordion-inner -->
		</div> <!-- /.accordion-body -->
	</div> <!-- /.accordion-group -->
	<?php ++$c; endif ?>

	<?php if (verifier('voir_historique_groupes') && count($ListerGroupes) > 0): ?>
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

	<?php if (verifier('ips_analyser') && count($ListerIPs)): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" href="#profile-infos-ips">
                <?php echo count($ListerIPs) ?> adresse<?php echo pluriel(count($ListerIPs)) ?> IP
            </a>
        </div>
        <div id="profile-infos-ips" class="accordion-body collapse">
            <div class="accordion-inner">
            	<table class="table">
					<thead>
						<tr>
							<th>IP</th>
							<th>Proxy</th>
							<th>Date</th>
							<th>Géolocalisation</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($ListerIPs as $ip): ?>
						<tr>
							<td>
								<?php echo long2ip($ip['ip_ip']); ?>
							</td>
							<td>
								<?php if(!empty($ip['ip_proxy'])): ?>
									<?php echo long2ip($ip['ip_proxy']) ?>
								<?php endif ?>
							</td>
							<td>
								<?php echo dateformat($ip['ip_date_debut'], DATE) ?> &rarr; 
								<?php echo dateformat($ip['ip_date_last'], DATE) ?>
							</td>
							<td>
								<?php echo !empty($ip['ip_localisation']) ? htmlspecialchars($ip['ip_localisation']) : 'Inconnue' ?>
							</td>
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