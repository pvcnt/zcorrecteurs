<table class="UI_items messages">
	<thead>
		<tr>
			<td colspan="2">Page : <?php echo implode($Pages); ?></td>
		</tr>
		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="2">Page : <?php echo implode($Pages); ?></td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($Resultats as $result){ ?>
		<tr class="header_message">
			<td class="pseudo_membre">
				<?php if($result['utilisateur_pseudo']){ ?>
				<a href="/membres/profil-<?php echo $result['utilisateur_id']; ?>-<?php echo rewrite($result['utilisateur_pseudo']); ?>.html"
					style="color: <?php echo $result['groupe_class']; ?>;" rel="nofollow">
				<?php } echo htmlspecialchars($result['utilisateur_pseudo']); if(!empty($result['utilisateur_id'])) { ?>
				</a>
				<?php } ?>
			</td>
			<td class="dates">
				Posté <?php echo dateformat($result['message_date'], MINUSCULE); ?> -
				<?php if($result['sujet_resolu']){ ?>
				<img src="/bundles/zcoforum/img/resolu.png" alt="Sujet résolu" />
				<?php } if($result['sujet_ferme']){ ?>
				<img src="/bundles/zcoforum/img/cadenas.png" alt="Sujet fermé" />
				<?php } ?>

				<strong>
					<a href="/forum/sujet-<?php echo $result['sujet_id']; ?>-<?php echo $result['message_id']; ?>-<?php echo rewrite($result['sujet_titre']); ?>.html">
						<?php echo htmlspecialchars($result['sujet_titre']); ?>
					</a>
				</strong>
			</td>
		</tr>

		<tr>
			<td class="infos_membre">
				<?php if(!empty($result['utilisateur_avatar'])){ ?>
				<a href="/membres/profil-<?php echo $result['message_auteur']; ?>-<?php echo rewrite($result['utilisateur_pseudo']); ?>.html" rel="nofollow">
                    <?php echo $view['messages']->afficherAvatar($result) ?>
				</a><br />
				<?php } ?>

				<?php echo $view->get('messages')->afficherGroupe($result) ?>
			</td>

			<td class="message">
				<div class="msgbox"><?php echo $view['messages']->parse($result['message_texte'], $result['message_id']); ?></div>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
