<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des billets rédigés par <?php echo $InfosUtilisateur['utilisateur_pseudo'] ?></h1>

<?php
if(!empty($InfosUtilisateur['utilisateur_avatar']))
{
?>
	<p class="centre"><?php echo $view['messages']->afficherAvatar($InfosUtilisateur) ?></p>
<?php
}
?>
<p class="centre"><a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong></a> a rédigé <?php echo count($ListerBillets) ; ?> billet<?php echo pluriel(count($ListerBillets)); ?>.</p>

<?php if($ListerBillets){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 20%;">Titre</th>
			<th style="width: 20%;">Auteur(s)</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 10%;">Commentaires</th>
			<?php if(verifier('blog_editer_valide') OR verifier('blog_devalider')){ ?>
			<th style="width:  5%;">Actions</th>
			<?php } ?>
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
				<a href="billet-<?php echo $valeur['blog_id']; ?>-<?php echo rewrite($valeur['version_titre']); ?>.html"><?php echo htmlspecialchars($valeur['version_titre']); ?></a>
			</td>
			<td class="centre">
				<?php foreach($Auteurs as $a){ ?>
				<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html"<?php if($a['auteur_statut'] == 3) echo ' class="gras"'; elseif($a['auteur_statut'] == 1) echo ' class="italique"'; ?>><?php echo htmlspecialchars($a['utilisateur_pseudo']); ?></a><br />
				<?php } ?>
			</td>
			<td class="centre"><?php echo dateformat($valeur['blog_date_edition']); ?></td>
			<td class="centre"><a href="billet-<?php echo $valeur['blog_id']; ?>-<?php echo $valeur['lunonlu_id_commentaire']; ?>-<?php echo rewrite($valeur['version_titre']); ?>.html#commentaires" title="Accéder aux commentaires de ce billet"><?php echo $valeur['blog_nb_commentaires'] ; ?></a></td>
			<?php if(verifier('blog_editer_valide') OR verifier('blog_devalider') OR $redacteur){ ?>
			<td class="centre">
				<?php
				if(($valeur['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))){ ?>
					<a href="editer-<?php echo $valeur['blog_id']; ?>.html"><img src="/img/editer.png" title="Éditer ce billet" alt="Éditer ce billet" /></a>
				<?php } if(verifier('blog_devalider') && $valeur['blog_etat'] == BLOG_VALIDE){ ?>
					<a href="devalider-<?php echo $valeur['blog_id']; ?>.html" title="Dévalider ce billet"><img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" /></a>
				<?php } ?>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else { ?>
<p>Ce membre n'a pas encore rédigé de billet.</p>
<?php } ?>
