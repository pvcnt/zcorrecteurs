<?php if(!empty($ListerCommentaires)){ ?>
<h2>15 derniers commentaires</h2>

<div id="derniers_msg">
	<table class="UI_items messages" id="commentaires">
		<thead>
			<tr>
				<th style="width: 13%;">Auteur</th>
				<th style="width: 87%;">Message</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($ListerCommentaires as $clef => $valeur){ ?>
			<tr class="header_message">
				<td class="pseudo_membre">
					<?php if(!empty($valeur['id_auteur'])) echo $view->get('messages')->colorerPseudo($valeur, 'id_auteur', 'pseudo_auteur') ?>
				</td>
				<td class="dates">
					<span id="m<?php echo $valeur['commentaire_id'];?>">
						<a href="billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo $valeur['commentaire_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">#</a>
					</span>

					Ajouté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE); ?>

					<!--<a href="#" title="Citer le commentaire" onclick="">
						<img src="/bundles/zcoforum/img/citer.png" alt="Citer" />
					</a>-->

					<?php if(
						$_SESSION['id'] != $valeur['id_auteur']
						AND verifier('mp_voir')
						AND ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1)
					)
					{
					?>
					<a href="/mp/nouveau-<?php echo $valeur['id_auteur']; ?>.html"><img src="/bundles/zcoforum/img/envoyer_mp.png" alt="MP" title="Envoyer un message privé" /></a>
					<?php } ?>
				</td>
			</tr>

			<tr>
				<td class="infos_membre">
					<?php if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />' ; } ?>

					<?php echo $view->get('messages')->afficherAvatar($valeur, 'avatar_auteur') ?><br/>

					<?php echo $view->get('messages')->afficherGroupe($valeur) ?>
					<?php if(!empty($valeur['utilisateur_titre'])) echo htmlspecialchars($valeur['utilisateur_titre']).'<br />'; ?>
				</td>

				<td class="message">
					<div class="msgbox">
						<?php echo $view['messages']->parse($valeur['commentaire_texte'], array(
							'files.entity_id' => $valeur['commentaire_id'],
							'files.entity_class' => 'BlogCommentaire',
						)); ?>

						<?php if(!empty($valeur['id_edite'])){ ?>
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
</div>
<?php } ?>
