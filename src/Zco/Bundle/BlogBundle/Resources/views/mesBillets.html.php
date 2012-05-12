<?php $view->extend('::layouts/default.html.php') ?>

<h1>Mes billets</h1>

<p>
	Bienvenue sur l'interface de gestion de vos billets ! Cette page liste
	tous les billets auxquels vous avez participé, triés par état. Vous pouvez
	donc les lire, les modifier, et envoyer à la validation ceux qui sont en
	cours de rédaction.<br />
	Merci à tous ceux qui contribueront à la vie du site ainsi !
</p>

<p class="gras centre"><a href="ajouter.html">Ajouter un nouveau billet</a></p>

<fieldset>
	<legend>Filtrer les billets</legend>
	<form method="get" action="">
		<select name="id" id="id" onchange="document.location = 'mes-billets-' + this.value + '.html';">
			<option value="0"<?php if(empty($_GET['id'])) echo ' selected="selected"'; ?>>Tous</option>
			<option value="<?php echo BLOG_BROUILLON; ?>"<?php if(!empty($_GET['id']) && $_GET['id'] == BLOG_BROUILLON) echo ' selected="selected"'; ?>>Brouillon</option>
			<option value="<?php echo BLOG_REFUSE; ?>"<?php if(!empty($_GET['id']) && $_GET['id'] == BLOG_REFUSE) echo ' selected="selected"'; ?>>Refusé</option>
			<option value="<?php echo BLOG_PREPARATION; ?>"<?php if(!empty($_GET['id']) && $_GET['id'] == BLOG_PREPARATION) echo ' selected="selected"'; ?>>En cours de préparation</option>
			<option value="<?php echo BLOG_PROPOSE; ?>"<?php if(!empty($_GET['id']) && $_GET['id'] == BLOG_PROPOSE) echo ' selected="selected"'; ?>>Proposé</option>
			<option value="<?php echo BLOG_VALIDE; ?>"<?php if(!empty($_GET['id']) && $_GET['id'] == BLOG_VALIDE) echo ' selected="selected"'; ?>>Validé</option>
		</select>
		<noscript><input type="submit" value="Aller" /></noscript>
	</form>
</fieldset>

<?php if($ListerBillets){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 30%;">Titre</th>
			<th style="width: 15%;">Auteur(s)</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 15%;">Dernière modification</th>
			<th style="width: 10%;">État</th>
			<th style="width: 5%;">Modifier</th>
			<th style="width: 5%;">Proposer</th>
			<th style="width: 5%;">Supprimer</th>
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
				<a href="/blog/billet-<?php echo $valeur['blog_id']; ?>-<?php echo $valeur['lunonlu_id_commentaire']; ?>-<?php echo rewrite($valeur['version_titre']); ?>.html#m<?php echo $valeur['lunonlu_id_commentaire']; ?>" title="Aller au dernier message lu"><img src="/bundles/zcoforum/img/fleche.png" alt="Dernier message lu" /></a>
				<?php } ?>

				<a href="admin-billet-<?php echo $valeur['blog_id']; ?>-<?php echo rewrite($valeur['version_titre']); ?>.html">
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
			<td class="centre">
				<?php echo dateformat($valeur['blog_date']); ?>
			</td>
			<td class="centre">
				<?php echo dateformat($valeur['blog_date_edition']); ?>
			</td>
			<td class="centre">
				<?php echo $Etats[$valeur['blog_etat']]; ?>
			</td>
			<td class="centre">
				<?php if((in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $redacteur == true) || (in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && verifier('blog_editer_brouillons')) || ($valeur['blog_etat'] == BLOG_PREPARATION && verifier('blog_editer_preparation')) ||
				($valeur['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))){ ?>
				<a href="editer-<?php echo $valeur['blog_id']; ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
				<?php } else echo '-'; ?>
			</td>
			<td class="centre">
				<?php if(in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $createur == true){ ?>
				<a href="proposer-<?php echo $valeur['blog_id']; ?>.html" title="Proposer ce billet à la validation"><img src="/bundles/zcoblog/img/proposer.png" alt="Proposer" /></a>
				<?php } ?>
				<?php if(verifier('blog_choisir_etat') && !in_array($valeur['blog_etat'], array(BLOG_VALIDE, BLOG_PROPOSE))){ ?>
				<a href="valider-<?php echo $valeur['blog_id']; ?>.html" title="Valider ce billet"><img src="/bundles/zcoblog/img/valider.png" alt="Valider" /></a>
				<?php } elseif(verifier('blog_devalider') && $valeur['blog_etat'] == BLOG_VALIDE){ ?>
				<a href="devalider-<?php echo $valeur['blog_id']; ?>.html" title="Dévalider ce billet"><img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" /></a>
				<?php } if((!in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) || $createur == false) && (!verifier('blog_choisir_etat') || in_array($valeur['blog_etat'], array(BLOG_VALIDE, BLOG_PROPOSE))) && (!verifier('blog_devalider') || $valeur['blog_etat'] != BLOG_VALIDE)) echo '-'; ?>
			</td>
			<td class="centre">
				<?php if((in_array($valeur['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $createur == true) || verifier('blog_supprimer')){ ?>
				<a href="supprimer-<?php echo $valeur['blog_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Vous n'avez aucun billet.</p>
<?php } ?>
