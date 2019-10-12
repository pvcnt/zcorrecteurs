<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Mes billets</h1>

<p>
	Bienvenue sur l'interface de gestion de vos billets ! Cette page liste
	tous les billets auxquels vous avez participé, triés par état. Vous pouvez
	donc les lire, les modifier, et envoyer à la validation ceux qui sont en
	cours de rédaction.<br />
	Merci à tous ceux qui contribueront à la vie du site ainsi !
</p>

<p class="bold center"><a href="<?php echo $view['router']->path('zco_blog_new') ?>">Ajouter un nouveau billet</a></p>

<form method="get" action="">
    <select name="id" id="id" onchange="document.location = '<?php echo $view['router']->path('zco_blog_mine') ?>?etat=' + this.value;">
        <option value=""<?php if(empty($status)) echo ' selected="selected"'; ?>>Tous</option>
        <option value="<?php echo BLOG_BROUILLON; ?>"<?php if(!empty($status) && $status == BLOG_BROUILLON) echo ' selected="selected"'; ?>>Brouillon</option>
        <option value="<?php echo BLOG_REFUSE; ?>"<?php if(!empty($status) && $status == BLOG_REFUSE) echo ' selected="selected"'; ?>>Refusé</option>
        <option value="<?php echo BLOG_PROPOSE; ?>"<?php if(!empty($status) && $status == BLOG_PROPOSE) echo ' selected="selected"'; ?>>Proposé</option>
        <option value="<?php echo BLOG_VALIDE; ?>"<?php if(!empty($status) && $status == BLOG_VALIDE) echo ' selected="selected"'; ?>>Validé</option>
    </select>
    <noscript><input type="submit" value="Aller" /></noscript>
</form>

<?php if($ListerBillets){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width: 30%;">Titre</th>
			<th>Auteur(s)</th>
			<th>Création</th>
			<th>Dernière modification</th>
			<th>État</th>
			<th>Publier</th>
			<th>Supprimer</th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach($ListerBillets as $cle => $valeur){
			$Auteurs = $BilletsAuteurs[$valeur['blog_id']];
			$createur = false;
			$redacteur = false;
		?>
		<tr>
			<td>
				<?php if(!empty($valeur['lunonlu_id_commentaire']) && verifier('connecte')){ ?>
				<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $valeur['blog_id'], 'slug' => rewrite($valeur['version_titre']), 'c' => $valeur['lunonlu_id_commentaire']]) ?>#m<?php echo $valeur['lunonlu_id_commentaire']; ?>" title="Aller au dernier message lu"><img src="/bundles/zcocontent/img/fleche.png" alt="Dernier message lu" /></a>
				<?php } ?>

				<a href="<?php echo $view['router']->path('zco_blog_manage', ['id' => $valeur['blog_id']]) ?>">
					<?php echo htmlspecialchars($valeur['version_titre']); ?>
				</a>
			</td>
			<td>
				<?php
				foreach($Auteurs as $a){
					if($a['utilisateur_id'] == $_SESSION['id'])
					{
						if($a['auteur_statut'] == 3)
							$createur = true;
						if($a['auteur_statut'] > 1)
							$redacteur = true;
					}
				?>
				<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html" class="<?php echo $AuteursClass[$a['auteur_statut']]; ?>"><?php echo htmlspecialchars($a['utilisateur_pseudo']); ?></a><br />
				<?php } ?>
			</td>
			<td class="center">
				<?php echo dateformat($valeur['blog_date']); ?>
			</td>
			<td class="center">
				<?php echo dateformat($valeur['blog_date_edition']); ?>
			</td>
			<td class="center">
				<?php echo $Etats[$valeur['blog_etat']]; ?>
			</td>
			<td class="center">
				<?php if(verifier('blog_valider') && !in_array($valeur['blog_etat'], array(BLOG_VALIDE, BLOG_PROPOSE))){ ?>
				<a href="<?php echo $view['router']->path('zco_blog_publish', ['id' => $valeur['blog_id']]) ?>" title="Valider ce billet"><img src="/bundles/zcoblog/img/valider.png" alt="Valider" /></a>
				<?php } elseif(verifier('blog_valider') && $valeur['blog_etat'] == BLOG_VALIDE){ ?>
				<a href="<?php echo $view['router']->path('zco_blog_unpublish', ['id' => $valeur['blog_id']]) ?>" title="Dévalider ce billet"><img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" /></a>
				<?php } ?>
			</td>
			<td class="center">
				<?php if((in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $createur == true) || verifier('blog_editer_valide')){ ?>
				<a href="<?php echo $view['router']->path('zco_blog_delete', ['id' => $valeur['blog_id']]) ?>"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Vous n'avez aucun billet.</p>
<?php } ?>
