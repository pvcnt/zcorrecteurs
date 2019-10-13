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
					<?php if (!empty($valeur['id_auteur'])) { ?>
                        <a href="<?php echo $view['router']->path('zco_user_profile', ['id' => $valeur['id_auteur'], 'slug' => rewrite($valeur['pseudo_auteur'])]) ?>">
                            <?php echo htmlspecialchars($valeur['pseudo_auteur']) ?>
                        </a>
                    <?php } ?>
				</td>
				<td class="dates">
					<span id="m<?php echo $valeur['commentaire_id'];?>">
						<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre']), 'c' => $valeur['commentaire_id']]) ?>">#</a>
					</span>

					Ajouté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE); ?>
				</td>
			</tr>

			<tr>
				<td class="infos_membre">
					<?php if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />' ; } ?>
					<?php echo $view['messages']->afficherAvatar($valeur, 'avatar_auteur') ?><br/>
					<?php echo $view['messages']->afficherGroupe($valeur) ?>
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
