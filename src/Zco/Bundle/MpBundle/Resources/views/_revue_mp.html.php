<h1>15 derniers messages</h1>

<div id="derniers_msg">
	<table class="UI_items messages">
		<thead>
			<tr>
				<th style="width: 13%;">Auteur</th>
				<th style="width: 87%;">Message</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if($RevueMP)
		{
			foreach($RevueMP as $clef => $valeur)
			{
			?>
			<tr class="header_message">
			<td class="pseudo_membre">
			<img src="/img/<?php echo $valeur['statut_connecte']; ?>" alt="<?php echo $valeur['statut_connecte_label']; ?>" title="<?php echo $valeur['statut_connecte_label']; ?>" />
			<a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
			<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>
			</a>
			</td>
			<td class="dates">
				<span id="m<?php echo $valeur['mp_message_id'];?>"><a href="lire-<?php echo $_GET['id'].'-'.$valeur['mp_message_id'].'.html'; ?>" rel="nofollow">#</a></span>
				Posté <?php echo dateformat($valeur['mp_message_date'], MINUSCULE); ?>
			</td>
		</tr>
		<tr>
			<td class="infos_membre">
			<?php
				if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />'; }
				if(!empty($valeur['utilisateur_avatar']))
				{
				?>
				<a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html">
                    <?php echo $view['messages']->afficherAvatar($valeur) ?>
                </a><br />
				<?php
				}
			echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
			<?php if(!empty($valeur['utilisateur_titre']))
			{
				echo htmlspecialchars($valeur['utilisateur_titre']);
			}
			?>
			</td>
			<td class="message">
				<div class="msgbox">
					<?php
					//Affichage du message
					echo $view['messages']->parse($valeur['mp_message_texte']);
					?>
					<?php
					if(!empty($valeur['utilisateur_signature']))
					{
					?>
					<div class="signature"><hr />
					<?php echo $view['messages']->parse($valeur['utilisateur_signature']); ?>
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
			<td colspan="2" class="centre">Ce MP ne contient pas de message.</td>
		</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>
