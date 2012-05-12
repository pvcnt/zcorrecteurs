<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des billets en ligne</h1>

<p>
	Dans le tableau ci-dessous se trouvent listés les billets
	<strong>en ligne</strong>.
</p>

<?php if(!empty($ListerBillets)){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				Page : <?php foreach($ListePages as $p) echo $p; ?>
			</td>
		</tr>
		<tr>
			<th style="width: 20%;">Titre</th>
			<th style="width: 20%;">Auteur(s)</th>
			<th style="width: 10%;">Nb. vues</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 15%;">Publication</th>
			<?php if(verifier('blog_editer_valide')){ ?>
			<th style="width: 5%;">Éditer</th>
			<?php } if(verifier('blog_devalider')){ ?>
			<th style="width: 5%;">Dévalider</th>
			<?php } if(verifier('blog_supprimer')){ ?>
			<th style="width: 5%;">Supprimer</th>
			<?php } ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				Page : <?php foreach($ListePages as $p) echo $p; ?>
			</td>
		</tr>
	</tfoot>

	<tbody>
		<?php
		foreach($ListerBillets as $b){
		?>
		<tr>
			<td>
				<a href="billet-<?php echo $b['blog_id']; ?>-<?php echo rewrite($b['version_titre']); ?>.html">
					<?php echo htmlspecialchars($b['version_titre']); ?>
				</a>
			</td>
			<td class="centre">
				<?php foreach($Auteurs[$b['blog_id']] as $a){ ?>
				<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html" class="<?php echo $AuteursClass[$a['auteur_statut']]; ?>">
					<?php echo htmlspecialchars($a['utilisateur_pseudo']); ?>
				</a><br />
				<?php } ?>
			</td>
			<td class="centre"><?php echo $b['blog_nb_vues'] ?></td>
			<td><?php echo dateformat($b['blog_date']); ?></td>
			<td><?php echo dateformat($b['blog_date_publication']); ?></td>
			<?php if(verifier('blog_editer_valide')){ ?>
			<td class="centre">
				<a href="admin-billet-<?php echo $b['blog_id']; ?>.html" title="Modifier ce billet">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</td>
			<?php } ?>
			<?php if(verifier('blog_devalider')){ ?>
			<td class="centre">
				<a href="devalider-<?php echo $b['blog_id']; ?>.html" title="Mettre hors ligne ce billet">
					<img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" />
				</a>
			</td>
			<?php } ?>
			<?php if(verifier('blog_supprimer')){ ?>
			<td class="centre">
				<a href="supprimer-<?php echo $b['blog_id']; ?>.html" title="Supprimer ce billet">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Il n'y a aucun billet en ligne.</p>
<?php } ?>
