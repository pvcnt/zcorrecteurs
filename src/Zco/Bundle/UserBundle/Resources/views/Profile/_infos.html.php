<div class="accordion">
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