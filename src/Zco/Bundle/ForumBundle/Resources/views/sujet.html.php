<?php $view->extend('::layouts/default.html.php') ?>

<?php $view['slots']->start('meta') ?>
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="zcorrecteurs" />
<meta name="twitter:url" content="<?php echo $view['router']->url('zco_forum_showTopic', ['id' => $InfosSujet['sujet_id'], 'slug' => rewrite($InfosSujet['sujet_titre'])]) ?>" />
<meta name="twitter:description" content="<?php echo mb_substr(htmlspecialchars(strip_tags(str_replace("\n", ' ', $PremierMessage['message_texte']))), 0, 250) ?>" />
<meta name="twitter:title" content="<?php echo htmlspecialchars($InfosSujet['sujet_titre']) ?>" />
<?php if ($PremierMessage['auteur_avatar']): ?>
    <meta name="twitter:image" content="<?php echo $view['messages']->avatarUrl($PremierMessage, 'auteur_avatar') ?>" />
<?php endif ?>
<?php $view['slots']->stop() ?>

<h1 id="titre">
	<?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?>
</h1>

<?php if(!empty($InfosSujet['sujet_sous_titre'])){ ?>
	<h2 id="sous_titre">
		<?php echo htmlspecialchars($InfosSujet['sujet_sous_titre']); ?>
	</h2>
<?php } ?>

<?php if(!empty($InfosSujet['sujet_corbeille'])){ ?>
<div class="UI_errorbox">
	Ce message a été jeté à la corbeille !
	<a href="<?php echo $view['router']->path('zco_forum_trash', ['id' => $InfosSujet['sujet_id'], 'status' => 0, 'token' => $_SESSION['token']]) ?>">
		Restaurer le sujet
	</a>
</div>
<?php } ?>

<?php echo $SautRapide; ?>

<?php if($InfosSujet['sujet_resolu']){ ?>
<p class="sujet_resolu">
	<img src="/pix.gif" class="fff accept" alt="Résolu" title="Résolu" />
	Le problème de ce sujet a été résolu.
</p>
<?php } ?>

<?php
//Si le sujet est un sondage, on affiche le sondage en haut.
if($InfosSujet['sujet_sondage'] > 0)
{
	include(__DIR__.'/sondage.html.php');
}
?>

<table class="UI_items messages">
	<thead>
		<tr>
			<td colspan="2">Page :
			<?php
			foreach($tableau_pages as $element)
				echo $element;
			?>
			</td>
		</tr>
		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="2">Page :
			<?php
			foreach($tableau_pages as $element)
			{
				echo $element;
			}
			?>
			</td>
		</tr>
	</tfoot>

	<tbody>
	<?php
	//Listage des messages
	if($ListerMessages)
	{
		$numero_message = 0;
		$cache_signatures = array();
		foreach($ListerMessages as $clef => $valeur)
		{
			$numero_message++;
		?>
		<tr class="header_message">
			<td class="pseudo_membre">
				<?php if(!empty($valeur['auteur_groupe'])) { ?>
				<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html">
				<?php } echo htmlspecialchars($valeur['auteur_message_pseudo']); if(!empty($valeur['auteur_groupe'])) { ?>
				</a>
				<?php } ?>
			</td>
			<td class="dates">
				<?php
				//Indiquer le message comme ayant aidé
				if (	!( // Pas le premier message du sujet
						$page == 1 &&
						$numero_message == 1
						)
				&& (
					(
						verifier('indiquer_ses_messages_aide', $InfosSujet['sujet_forum_id'])
						&& $_SESSION['id'] == $InfosSujet['sujet_auteur']
					)
					|| verifier('indiquer_messages_aide', $InfosSujet['sujet_forum_id'])
				))
				{
					if($valeur['message_help'])
					{
					?>
					<span class="commandes_textuelles">
						<a href="<?php echo $view['router']->path('zco_forum_markHelped', ['id' => $valeur['message_id'], 'status' => 0, 'token' => $_SESSION['token']]) ?>">
							<img src="/pix.gif" class="fff delete icone_commande" alt="Indiquer que cette réponse de m'a pas aidé" />
							Cette réponse ne m'a pas aidé
						</a>
					</span>
					<?php } else{ ?>
					<span class="commandes_textuelles">
						<a href="<?php echo $view['router']->path('zco_forum_markHelped', ['id' => $valeur['message_id'], 'status' => 1, 'token' => $_SESSION['token']]) ?>">
							<img src="/pix.gif" class="fff accept icone_commande" alt="Indiquer que cette réponse m'a aidé" />
							Cette réponse m'a aidé
						</a>
					</span>
					<?php
					}
				}
				//Date d'envoi du message
				?>
				<span id="m<?php echo $valeur['message_id'];?>"><a href="<?php echo $view['router']->path('zco_forum_showTopic', ['id' => $InfosSujet['sujet_id'], 'c' => $valeur['message_id'], 'slug' => rewrite($InfosSujet['sujet_titre'])]) ?>" rel="nofollow">#</a></span>
				Posté <?php echo dateformat($valeur['message_date'], MINUSCULE); ?>
				<?php
				//Edition du message
				if
				(
					(
						(
							verifier('editer_ses_messages', $InfosSujet['sujet_forum_id']) AND $_SESSION['id'] == $valeur['message_auteur']
						)
						OR
						(
							verifier('editer_messages_autres', $InfosSujet['sujet_forum_id'])
						)
					)
					AND !$InfosSujet['sujet_corbeille'] AND
					(
						verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) OR !$InfosSujet['sujet_ferme']
					)
				)
				{
				?>
				<a href="<?php echo $view['router']->path('zco_forum_edit', ['id' => $valeur['message_id']]) ?>">
					<img src="/pix.gif" class="fff pencil" alt="Éditer" title="Éditer" />
				</a>
				<?php
				}
				//Suppression du sujet par le premier message
				if($valeur['message_id'] == $InfosSujet['sujet_premier_message'] && verifier('suppr_sujets', $InfosSujet['sujet_forum_id']))
				{
				?>
                <a href="<?php echo $view['router']->path('zco_forum_delete', ['id' => $InfosSujet['sujet_id']]) ?>">
					<img src="/pix.gif" class="fff cross" alt="Supprimer le sujet" title="Supprimer le sujet" />
				</a>
				<?php
				}
				//Supppression du message
				elseif(
					(
						verifier('suppr_messages', $InfosSujet['sujet_forum_id'])
						|| (verifier('suppr_ses_messages', $InfosSujet['sujet_forum_id']) && $valeur['message_auteur'] == $_SESSION['id'])
					)
					&& !$InfosSujet['sujet_corbeille']
					&&
					(
						!$InfosSujet['sujet_ferme']
						|| verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id'])
					)
				)
				{
				?>
				<a href="supprimer-message-<?php echo $valeur['message_id']; ?>.html">
					<img src="/pix.gif" class="fff cross" alt="Supprimer le message" title="Supprimer le message" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="infos_membre">
				<?php if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />' ; } ?>
				<?php if(!empty($valeur['auteur_avatar'])){ ?>
				<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html" rel="nofollow">
                    <?php echo $view['messages']->afficherAvatar($valeur, 'auteur_avatar') ?>
                </a>
				<br />

				<?php }	if(verifier('voir_nb_messages')){ ?>
				Messages : <?php echo $valeur['utilisateur_forum_messages']; ?><br />

				<?php } echo $view['messages']->afficherGroupe($valeur).'<br/>'; ?>
			</td>
			<td class="message<?php if($valeur['message_help']) echo ' bonne_reponse'; ?>">
				<div class="msgbox">
					<?php
					//En cas de reprise du dernier message
					if($numero_message == 1 AND $page > 1 AND $page <= $NombreDePages)
					{
						echo '<p class="gras centre">Reprise du dernier message de la page précédente :</p><br />';
					}

					//Si le message a aidé
					if($valeur['message_help'])
					{
					?>
					<div class="info_bonne_reponse"><img src="/bundles/zcoforum/img/resolu.png" alt="Cette réponse a aidé l'auteur du sujet" title="Cette réponse a aidé l'auteur du sujet" /> Cette réponse a aidé l'auteur du sujet.</div>
					<?php
					}

					//Affichage du corps du message
					echo $view['messages']->parse($valeur['message_texte'], array(
						'core.anchor_prefix' => $valeur['message_id'],
						'files.entity_id' => $valeur['message_id'],
						'files.entity_class' => 'ForumMessage',
					));

					//Affichage de la notification d'édition si besoin
					if(!empty($valeur['message_edite_auteur']))
					{
					?>
					<div class="message_edite">
						<?php if($valeur['message_auteur'] != $valeur['message_edite_auteur']){ ?>
						<span style="color: red;">
						<?php } ?>
						Modifié <?php echo dateformat($valeur['message_edite_date'], MINUSCULE); ?> par
						<?php if(!empty($valeur['auteur_edition_id'])){	?>
						<a href="/membres/profil-<?php echo $valeur['message_edite_auteur']; ?>-<?php echo rewrite($valeur['auteur_edition_pseudo']); ?>.html" rel="nofollow">
						<?php } ?>
						<?php echo htmlspecialchars($valeur['auteur_edition_pseudo']); ?>
						<?php if(!empty($valeur['auteur_edition_id'])) { ?>
						</a>
						<?php } ?>
						<?php if($valeur['message_auteur'] != $valeur['message_edite_auteur']){ ?>
						</span>
						<?php } ?>
					</div>
					<?php } ?>

					<?php if(!empty($valeur['auteur_message_signature'])){ ?>
					<div class="signature"><hr />
					<?php
					if (!isset($cache_signatures[$valeur['message_auteur']]))
					{
						$cache_signatures[$valeur['message_auteur']] = $view['messages']->parse($valeur['auteur_message_signature']);
					}
					echo $cache_signatures[$valeur['message_auteur']];
					?>
					</div>
					<?php } ?>
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

<?php
echo $SautRapide;

$ReponseRapide = '
<div id="reponse_rapide">
<form action="' . $view['router']->path('zco_forum_reply', ['id' => $InfosSujet['sujet_id']])  . '" method="post">
	<fieldset id="rep_rapide">
		Réponse rapide :<br />
		<textarea name="texte" id="texte" tabindex="10" cols="40" rows="10" class="zcode_rep_rapide"></textarea>
		<br />
		<input type="submit" name="send_reponse_rapide" value="Envoyer" tabindex="20" accesskey="s" /> <input type="submit" name="plus_options" value="Plus d\'options" tabindex="30" />
	</fieldset>
</form>
</div>';

if(!$InfosSujet['sujet_corbeille'])
{
	if($InfosSujet['sujet_ferme'] AND verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
        $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/save.js');
        echo $ReponseRapide;
	}
	elseif(verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) AND !$InfosSujet['sujet_ferme'])
	{
        $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/save.js');
        echo $ReponseRapide;
	}
}

if($afficher_options)
{
	include(__DIR__.'/_options_bas_sujet.html.php');
}
?>
