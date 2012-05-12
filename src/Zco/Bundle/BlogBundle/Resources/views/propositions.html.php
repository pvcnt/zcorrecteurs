<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des billets proposés</h1>

<p>
	Dans le tableau ci-dessous se trouvent listés les billets
	<strong>proposés</strong>. Vous pouvez alors choisir de les valider, ou de
	les refuser en donnant un petit mot d'explication.<br />

	Vous trouverez aussi les billets <strong>en cours de préparation</strong>.
	Ils sont là en attendant leur publication définitive. Vous pouvez les
	retoucher en attendant.
</p>

<?php if($ListerBillets){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 20%;">Titre</th>
			<th style="width: 20%;">Auteur(s)</th>
			<th style="width: 10%;">État</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 14%;">Édition</th>
			<?php if(verifier('blog_editer_preparation')){ ?>
			<th style="width: 7%;">Éditer</th>
			<?php } if(verifier('blog_valider')){ ?>
			<th style="width: 7%;">Publier</th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
	<?php
	foreach($ListerBillets as $cle=>$valeur){
	?>
		<tr>
			<td>
				<a href="admin-billet-<?php echo $valeur['blog_id']; ?>.html">
					<?php echo htmlspecialchars($valeur['version_titre']); ?>
				</a>
			</td>
			<td>
				<?php foreach($Auteurs[$valeur['blog_id']] as $a){ ?>
				<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html" class="<?php echo $AuteursClass[$a['auteur_statut']]; ?>">
					<?php echo htmlspecialchars($a['utilisateur_pseudo']); ?>
				</a><br />
				<?php } ?>
			</td>
			<td>
				<?php if($valeur['blog_etat'] == BLOG_PREPARATION) echo 'En préparation'; else echo 'Proposé'; ?>
			</td>
			<td><?php echo dateformat($valeur['blog_date']); ?></td>
			<td><?php echo dateformat($valeur['blog_date_edition']); ?></td>
			<?php if(verifier('blog_editer_preparation')){ ?>
			<td class="centre">
				<?php if($valeur['blog_etat'] == BLOG_PREPARATION){ ?>
				<a href="editer-<?php echo $valeur['blog_id']; ?>.html" title="Modifier ce billet">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
				<?php } else echo '-'; ?>
			</td>
			<?php } ?>
			<?php if(verifier('blog_valider')){ ?>
			<td class="centre">
				<?php if(verifier('blog_valider') && $valeur['blog_etat'] == BLOG_PROPOSE){ ?>
				<a href="repondre-<?php echo $valeur['blog_id']; ?>.html">
					<img src="/bundles/zcoblog/img/valider.png" alt="Valider" />&nbsp;/&nbsp;<img src="/img/supprimer.png" alt="Valider" />
				</a>
				<?php } elseif(verifier('blog_valider') && $valeur['blog_etat'] == BLOG_PREPARATION){ ?>
				<a href="valider-<?php echo $valeur['blog_id']; ?>.html" title="Mettre en ligne">
					<img src="/bundles/zcoblog/img/valider.png" alt="Valider" />
				</a>
				<?php } ?>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else{ ?>
<p>Il n'y a aucun billet proposé.</p>
<?php } ?>
