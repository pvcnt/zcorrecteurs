<tr class="<?php echo empty($_GET['trash']) ? 'sous_cat' : 'sous_cat_trash trash' ?>">

	<?php if (empty($_GET['trash'])){ ?>
	<td class="centre puce">
		<?php if (empty($forum['cat_redirection'])){ ?>
			<img src="/pix.gif" class="fff <?php echo $Lu[$forum['cat_id']]['image']; ?>" title="<?php echo $Lu[$forum['cat_id']]['title']; ?>" alt="<?php echo $Lu[$forum['cat_id']]['title']; ?>" />
		<?php } else{ ?>
			<img src="/pix.gif" class="fff anchor" alt="Redirection" title="Ce forum est une redirection" />
		<?php } ?>
	</td>
	<?php } ?>

	<td class="nom_forum">
		<h3>
			<a href="<?php echo FormateURLCategorie($forum['cat_id']); if (!empty($_GET['trash'])) echo '?trash=1'; ?>">
				<?php echo htmlspecialchars($forum['cat_nom']); ?>
			</a>
		</h3>
		<?php echo htmlspecialchars($forum['cat_description']); ?>

		<?php if (!empty($forum['sous_forums'])){ ?>
		<div class="sous_forums">
			<strong>Sous-forums : </strong>

			<?php foreach ($forum['sous_forums'] as $cat){ ?>
				<?php if (empty($cat['cat_redirection'])) { ?>
					<img src="/bundles/zcoforum/img/sous_forum_<?php echo $cat['lunonlu_message_id'] == $cat['message_id'] ? 'lu' : 'nonlu' ?>.png" alt="<?php echo $cat['lunonlu_message_id'] == $cat['message_id'] ? 'Pas de nouvelle réponse' : 'Nouvelles réponses' ?>" title="<?php echo $cat['lunonlu_message_id'] == $cat['message_id'] ? 'Pas de nouvelle réponse' : 'De nouvelles réponses ont été ajoutées' ?>" />
				<?php } else{ ?>
					<img src="/pix.gif" class="fff anchor" alt="Redirection" title="Ce forum est une redirection" />
				<?php } ?>
				<a href="<?php echo FormateURLCategorie($cat['cat_id']); if(!empty($_GET['trash'])) echo '?trash=1'; ?>" title="<?php echo htmlspecialchars($cat['cat_description']); ?>">
					<?php echo htmlspecialchars($cat['cat_nom']); ?>
				</a>
			<?php } ?>
		</div>
		<?php } ?>
	</td>
	<?php
		if(empty($_GET['trash']))
		{
			if(!empty($forum['cat_redirection']))
			{
				echo '<td class="dernier_msg centre">-</td>';
			}
			elseif(empty($forum['cat_last_element']))
			{
				echo '<td class="dernier_msg centre">Aucun message</td>';
			}
			else
			{
				echo '<td class="dernier_msg">'.dateformat($forum['message_date']).'<br />
				Par ';
				if(!empty($forum['utilisateur_id']))
				{
					echo '<a href="/membres/profil-'.$forum['message_auteur'].'-'.rewrite($forum['utilisateur_pseudo']).'.html" rel="nofollow"><span style="color: '.$forum['groupe_class'].';">';
				}
				echo htmlspecialchars($forum['utilisateur_pseudo']);
				if(!empty($forum['utilisateur_id']))
				{
					echo '</span></a>';
				}
				echo '<br />
				Dans <a href="sujet-'.$forum['message_sujet_id'].'-'.$forum['message_id'].'-'.rewrite($forum['sujet_titre']).'.html">'.htmlspecialchars($forum['sujet_titre']).'</a></td>';
			}
		}
		else
		{
			echo '<td class="dernier_msg centre">'.$forum['nb_sujets_corbeille'].'</td>';
		}
		?>
</tr>