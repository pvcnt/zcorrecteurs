<?php $view->extend('::layouts/default.html.php') ?>

<h1>Billets refusés</h1>

<p>Dans le tableau ci-dessous se trouvent listés les billets <strong>refusés</strong>. Vous pouvez alors choisir de les
passer en brouillon si vous changez d'avis, ou de les supprimer définitivement.</p>

<?php if($ListerBillets){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 30%;">Titre</th>
			<th style="width: 25%;">Auteur(s)</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 15%;">Refus</th>
			<?php if(verifier('blog_editer_brouillons')){ ?>
			<th style="width: 5%;">Éditer</th>
			<?php } if(verifier('blog_supprimer')){ ?>
			<th style="width: 5%;">Supprimer</th>
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
			<td><?php echo dateformat($valeur['blog_date']); ?></td>
			<td><?php echo dateformat($valeur['blog_date_validation']); ?></td>
			<?php if(verifier('blog_editer_brouillons')){ ?>
			<td class="centre">
				<a href="editer-<?php echo $valeur['blog_id']; ?>.html" title="Modifier ce billet">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</td>
			<?php } ?>
			<?php if(verifier('blog_supprimer')){ ?>
			<td class="centre">
				<a href="supprimer-<?php echo $valeur['blog_id']; ?>.html">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
			</td>
			<?php } ?>
		</tr>
		<?php }	?>
	</tbody>
</table>

<?php } else{ ?>
<p>Il n'y a aucun billet refusé.</p>
<?php } ?>
