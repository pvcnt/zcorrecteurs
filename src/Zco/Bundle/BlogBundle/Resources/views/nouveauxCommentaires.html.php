<?php $view->extend('::layouts/default.html.php') ?>

<h1>Nouveaux commentaires</h1>

<?php if($ListerCommentaires): ?>
<p>Ici s'affichent les commentaires n'ayant pas encore été vus par un membre de l'équipe de modération.
Une fois que vous avez sélectionné les commentaires que vous avez lu en cochant la case correspondante
ou en cliquant dessus, pressez le bouton "Valider" pour les faire disparâitre de cette liste.</p>

<p class="rmq attention">En validant un commentaire, vous validez également tous les commentaires postés
avant dans le même billet ! Les commentaires sont affichés du plus ancien au plus récent.</p>
<form action="" method="post" id="nouveauxMessages">
<p style="text-align: right" id="boutons_selection">
	<input type="submit" value="Valider" />
</p>

<?php $view['javelin']->initBehavior('blog-new-comments') ?>
<table class="UI_items messages">
	<thead>
		<tr><td colspan="2">Page : <?php echo implode($ListePages) ?></td></tr>

		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr><td colspan="2">Page : <?php echo implode($ListePages) ?></td></tr>
	</tfoot>

	<tbody>
		<?php
		$numero_message = 0;
		foreach($ListerCommentaires as $clef => $valeur): ?>
		<tr class="header_message">
			<td class="pseudo_membre">
				<img src="/img/<?php echo $valeur['statut_connecte'] ?>" alt="<?php echo $valeur['statut_connecte_label'] ?>" title="<?php echo $valeur['statut_connecte_label'] ?>" />
				<?php if($valeur['id_auteur']) echo $view->get('messages')->colorerPseudo($valeur, 'id_auteur', 'pseudo_auteur') ?>
			</td>
			<td class="dates">
				<input type="checkbox"
				       name="commentaires[]"
				       value="<?php echo $valeur['commentaire_id'] ?>"
				       style="float: right; margin: 0px"
				/>
				<span id="m<?php echo $valeur['commentaire_id'] ?>"><a href="nouveaux-commentaires-<?php echo $valeur['commentaire_id'] ?>.html#m<?php echo $valeur['commentaire_id'] ?>">#</a></span>
				Posté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE) ?>
				<?php if(verifier('connecte') && ($valeur['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))): ?>
					<a href="ajouter-commentaire-<?php echo $_GET['id'] ?>-<?php echo $valeur['commentaire_id'] ?>.html"><img src="/bundles/zcoforum/img/citer.png" alt="Citer" title="Citer" /></a>
				<?php endif; if((($valeur['id_auteur'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires')) || verifier('blog_editer_commentaires')) && ($valeur['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))): ?>
					<a href="<?php echo 'editer-commentaire-'.$valeur['commentaire_id'] ?>.html"><img src="/img/editer.png" alt="Éditer" title="Éditer" /></a>
				<?php endif; if(verifier('blog_editer_commentaires')): ?>
					<a href="supprimer-commentaire-<?php echo $valeur['commentaire_id'] ?>.html"><img src="/img/supprimer.png" alt="Supprimer le commentaire" title="Supprimer le commentaire" /></a>
				<?php endif ?>
				 - <strong><a href="billet-<?php echo $valeur['blog_id'] ?>.html"><?php echo htmlspecialchars($valeur['version_titre']) ?></a></strong>
			</td>
		</tr>

		<tr>
			<td class="infos_membre">
				<?php echo $view->get('messages')->afficherAvatar($valeur, 'avatar_auteur') ?><br/>
				<?php echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
				<?php if(verifier('sanctionner')): ?>
					<a href="<?php echo $view['router']->path('zco_user_admin_punish', array('id' => htmlspecialchars($valeur['id_auteur']))) ?>">
						Sanctionner
					</a>
					(<a href="/membres/profil-<?php echo $valeur['id_auteur'] ?>-<?php echo rewrite($valeur['pseudo_auteur']) ?>.html#sanctions"><?php echo $valeur['nb_sanctions_auteur'] ?></a>)
				<?php elseif(verifier('voir_sanctions') && $valeur['nb_sanctions_auteur'] > 0): ?>
					<a href="/membres/profil-<?php echo $valeur['id_auteur'] ?>-<?php echo rewrite($valeur['pseudo_auteur']) ?>.html#sanctions">Sanction(s) : <?php echo $valeur['nb_sanctions_auteur'] ?></a>
				<?php endif; if(verifier('ips_analyser')): ?>
					<br /><br />IP : <a href="<?php echo $view['router']->path('zco_user_ips_analyze', ['ip' => long2ip($valeur['commentaire_ip'])]) ?>"><?php echo long2ip($valeur['commentaire_ip']) ?></a>
				<?php endif ?>
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
						Modifié <?php echo dateformat($valeur['commentaire_edite_date'], MINUSCULE) ?>
						par
						<?php if(!empty($valeur['id_edite'])){?>
						<a href="/membres/profil-<?php echo $valeur['id_edite'] ?>-<?php echo rewrite($valeur['pseudo_edite']) ?>.html">
						<?php }	?>
						<?php echo htmlspecialchars($valeur['pseudo_edite']) ?>
						<?php if(!empty($valeur['id_edite'])) { ?></a><?php } ?>
						<?php if($valeur['id_edite'] != $valeur['id_auteur']){ ?></span><?php } ?>
					</div>
					<?php } if(!empty($valeur['auteur_message_signature'])){ ?>
					<div class="signature"><hr />
					<?php echo $view['messages']->parse($valeur['signature_auteur']) ?>
					</div>
					<?php }	?>
					<div class="cleaner">&nbsp;</div>
				</div>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<p class="centre"><input type="submit" value="Valider les commentaires sélectionnés" /></p>
</form>
<?php else: ?>
	<p>Aucun nouveau commentaire.</p>
<?php endif ?>












