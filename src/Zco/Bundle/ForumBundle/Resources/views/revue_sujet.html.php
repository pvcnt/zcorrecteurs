<h1>15 derniers messages</h1>

<div id="derniers_msg">
	<table class="UI_items">
		<thead>
			<tr>
				<th style="width: xhr = new Request({method: 'post', url: '/forum/ajax-multi-citer.html',
    				onSuccess: function(text, xml){
    					$('texte').value += ($chk($('texte').value) ? "\n\n" : '')+text;
    				}
    			});
    			xhr.send('action=ajoute&url='+encodeURIComponent(elem.href));
    			return false;13%;">Auteur</th>
				<th style="width: 87%;">Message</th>
			</tr>
		</thead>
		<tbody>
		<?php
		//Ici on fait une boucle qui va nous lister les x derniers message du sujet.
		if($RevueSujet) //Si il y a au moins un message à lister, on liste !
		{
			foreach($RevueSujet as $clef => $valeur)
			{
			?>
			<tr class="header_message">
				<td class="pseudo_membre">
				<?php if(!empty($valeur['auteur_groupe'])) {?>
				<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
				<?php } ?>
				<?php echo htmlspecialchars($valeur['auteur_message_pseudo']); ?>
				<?php if(!empty($valeur['auteur_groupe'])) {?>
				</a>
				<?php } ?>
				</td>
				<td class="dates">
					<a href="sujet-<?php echo $_GET['id'].'-'.$valeur['message_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html'; ?>">#</a>
					Posté <?php echo dateformat($valeur['message_date'], MINUSCULE); ?>
					<a href="repondre-<?php echo $_GET['id'].'-'.$valeur['message_id'] ?>.html" class="lien_citer">
						<img src="/pix.gif" class="fff comments" alt="Citer" title="Citer" />
					</a>
				</td>
			</tr>
			<tr>
				<td class="infos_membre">
				<?php
					if(!empty($valeur['utilisateur_citation']))
					{
						echo htmlspecialchars($valeur['utilisateur_citation']).'<br />';
					}
					if(!empty($valeur['auteur_avatar']))
					{
					?>
					<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html">
                        <?php echo $view['messages']->afficherAvatar($valeur, 'auteur_avatar') ?>
                    </a><br />
					<?php
					}
					echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
				<?php if(!empty($valeur['utilisateur_titre']))
					echo '<br />'.htmlspecialchars($valeur['utilisateur_titre']); ?>
				</td>
				<td class="message">
					<div class="msgbox">
						<?php echo $view['messages']->parse($valeur['message_texte'], array(
							'core.anchor_prefix' => $valeur['message_id'],
							'files.entity_id' => $valeur['message_id'],
							'files.entity_class' => 'ForumMessage',
						)); ?>
						<?php
						if(!empty($valeur['message_edite_auteur']))
						{
						?>
						<div class="message_edite">
							<?php
							if($valeur['message_auteur'] != $valeur['message_edite_auteur'])
							{
							?>
								<span style="color: red;">
							<?php
							}
							?>
								Modifié <?php echo dateformat($valeur['message_edite_date'], MINUSCULE); ?>
								par
								<?php if(!empty($valeur['auteur_edition_id'])) { ?>
								<a href="/membres/profil-<?php echo $valeur['message_edite_auteur']; ?>-<?php echo rewrite($valeur['auteur_edition_pseudo']); ?>.html">
								<?php } ?>
								<?php echo htmlspecialchars($valeur['auteur_edition_pseudo']); ?>
								<?php if(!empty($valeur['auteur_edition_id'])) { ?>
								</a>
								<?php } ?>
							<?php
							if($valeur['message_auteur'] != $valeur['message_edite_auteur'])
							{
							?>
								</span>
							<?php
							}
							?>
						</div>
						<?php
						}
						if(!empty($valeur['auteur_message_signature']))
						{
						?>
						<div class="signature"><hr />
						<?php echo $view['messages']->parse($valeur['auteur_message_signature']); ?>
						</div>
						<?php
						}
						?>
						<div class="cleaner">&nbsp;</div>
					</div>
				</td>
			</tr>
			<?php
			}
		}
		else
		{
		?>
		<tr class="sous_cat">
			<td colspan="2" class="centre">Ce sujet ne contient pas de message.</td>
		</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>
