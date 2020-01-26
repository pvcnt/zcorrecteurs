<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste de tous les commentaires</h1>

<?php if($ListerCommentaires){ ?>
<table class="UI_items messages">
	<thead>
		<tr>
			<td colspan="2">Page :
				<?php foreach($ListePages as $element) echo $element.''; ?>
			</td>
		</tr>

		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr><td colspan="2">Page :
				<?php foreach($ListePages as $element) echo $element.''; ?>
			</td></tr>
	</tfoot>

	<tbody>
		<?php
		$numero_message = 0;
		foreach($ListerCommentaires as $clef => $valeur)
		{
		?>
		<tr class="header_message">
			<td class="pseudo_membre">
			<?php if(!empty($valeur['id_auteur'])) {?>
			<a href="/membres/profil-<?php echo $valeur['id_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
			<?php } ?>
			<?php echo htmlspecialchars($valeur['pseudo_auteur']); ?>
			<?php if(!empty($valeur['id_auteur'])) {?></a><?php } ?>
			</td>
			<td class="dates">
				<span id="m<?php echo $valeur['commentaire_id'];?>"><a href="tous-les-commentaires-<?php echo $valeur['commentaire_id']; ?>.html#m<?php echo $valeur['commentaire_id']; ?>">#</a></span>
				Posté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE); ?>
				<?php if(verifier('blog_commenter') && ($valeur['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))){ ?>
				<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>-<?php echo $valeur['commentaire_id']; ?>.html"><img src="/bundles/zcoforum/img/citer.png" alt="Citer" title="Citer" /></a>
				<?php } ?>
				<?php if((($valeur['id_auteur'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires')) || verifier('blog_editer_commentaires')) && ($valeur['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))){ ?>
				<a href="<?php echo 'editer-commentaire-'.$valeur['commentaire_id']; ?>.html"><img src="/img/editer.png" alt="Éditer" title="Éditer" /></a>
				<?php } if(verifier('blog_supprimer_commentaires')){ ?>
				<a href="supprimer-commentaire-<?php echo $valeur['commentaire_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer le commentaire" title="Supprimer le commentaire" /></a>
				<?php } ?>
				 - <strong><a href="billet-<?php echo $valeur['blog_id']; ?>.html"><?php echo htmlspecialchars($valeur['version_titre']); ?></a></strong>
			</td>
		</tr>

		<tr>
			<td class="infos_membre">
			<?php if(!empty($valeur['avatar_auteur'])){ ?>
				<a href="/membres/profil-<?php echo $valeur['id_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html">
                    <?php echo $view['messages']->afficherAvatar($valeur, 'avatar_auteur') ?>
                </a><br />
            <?php } ?>
            <?php echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
			<?php if(verifier('sanctionner')){ ?>
				<a href="<?php echo $view['router']->generate('zco_user_admin_punish', array('id' => htmlspecialchars($valeur['id_auteur']))) ?>">
					Sanctionner
				</a> (<a href="/membres/profil-<?php echo $valeur['id_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html#sanctions"><?php echo $valeur['nb_sanctions_auteur']; ?></a>)
			<?php } elseif(verifier('voir_sanctions') && $valeur['nb_sanctions_auteur'] > 0){ ?>
			<a href="/membres/profil-<?php echo $valeur['id_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html#sanctions">Sanction(s) : <?php echo $valeur['nb_sanctions_auteur']; ?></a>
			<?php } if(verifier('membres_avertir')){ ?>
			<br /><a href="<?php echo $view['router']->generate('zco_user_admin_warn', array('id' => htmlspecialchars($valeur['id_auteur']))) ?>">
				Avertir
			</a> : <a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html#avertos"><?php echo $valeur['pourcentage_auteur']; ?> %</a>
			<?php }	elseif(verifier('membres_voir_avertos') && $valeur['pourcentage_auteur'] > 0){ ?>
			<br /><a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['pseudo_auteur']); ?>.html#avertos">Averto(s) : <?php echo $valeur['pourcentage_auteur']; ?> %</a>
			<?php } if(verifier('ips_analyser')){
				echo '<br /><br />IP : <a href="/ips/analyser.html?ip='.long2ip($valeur['commentaire_ip']).'">'.long2ip($valeur['commentaire_ip']).'</a>';
			} ?>
			</td>

			<td class="message">
				<div class="msgbox">
					<?php echo $view['messages']->parse($valeur['commentaire_texte'], array(
						'files.entity_id' => $valeur['commentaire_id'],
						'files.entity_class' => 'BlogCommentaire',
					)) ?>

					<?php
					if(!empty($valeur['id_edite']))
					{
					?>
					<div class="message_edite">
						<?php if($valeur['id_edite'] != $valeur['id_auteur']){ ?>
						<span style="color: red;">
						<?php } ?>
						Modifié <?php echo dateformat($valeur['commentaire_edite_date'], MINUSCULE); ?>
						par
						<?php if(!empty($valeur['id_edite'])){?>
						<a href="/membres/profil-<?php echo $valeur['id_edite']; ?>-<?php echo rewrite($valeur['pseudo_edite']); ?>.html">
						<?php }	?>
						<?php echo htmlspecialchars($valeur['pseudo_edite']); ?>
						<?php if(!empty($valeur['id_edite'])) { ?></a><?php } ?>
						<?php if($valeur['id_edite'] != $valeur['id_auteur']){ ?></span><?php } ?>
					</div>
					<?php } if(!empty($valeur['auteur_message_signature'])){ ?>
					<div class="signature"><hr />
					<?php echo $view['messages']->parse($valeur['signature_auteur']); ?>
					</div>
					<?php }	?>
					<div class="cleaner">&nbsp;</div>
				</div>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun commentaire n'a été déposé.</p>
<?php } ?>
