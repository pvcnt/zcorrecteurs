<?php $view->extend('::layouts/default.html.php') ?>

<?php $view['slots']->start('meta') ?>
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="zcorrecteurs" />
<meta name="twitter:url" content="<?php echo URL_SITE ?>/forum/sujet-<?php echo $InfosSujet['sujet_id'] ?>-<?php echo rewrite($InfosSujet['sujet_titre']) ?>.html" />
<meta name="twitter:description" content="<?php echo mb_substr(htmlspecialchars(strip_tags(str_replace("\n", ' ', $PremierMessage['message_texte']))), 0, 250) ?>" />
<meta name="twitter:title" content="<?php echo htmlspecialchars($InfosSujet['sujet_titre']) ?>" />
<?php if ($PremierMessage['auteur_avatar']): ?>
    <meta name="twitter:image" content="<?php echo URL_SITE ?>/uploads/avatars/<?php echo htmlspecialchars($PremierMessage['auteur_avatar']); ?>" />
<?php endif ?>
<?php $view['slots']->stop() ?>

<h1 id="titre">
	<?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?>
</h1>
<?php $view['javelin']->initBehavior('edit-in-place', array(
	'id' => 'titre', 
	'callback' => '/forum/ajax-edit-in-place-titre.html',
	'options' => array('extraData' => array('id_suj' => $_GET['id'])),
)) ?>

<?php if(!empty($InfosSujet['sujet_sous_titre'])){ ?>
	<h2 id="sous_titre">
		<?php echo htmlspecialchars($InfosSujet['sujet_sous_titre']); ?>
	</h2>
	<?php $view['javelin']->initBehavior('edit-in-place', array(
		'id' => 'sous_titre', 
		'callback' => '/forum/ajax-edit-in-place-sous-titre.html',
		'options' => array('extraData' => array('id_suj' => $_GET['id'])),
	)) ?>
<?php } ?>

<?php if(!empty($InfosSujet['cat_reglement'])) echo '<div class="reglement">'.$view['messages']->parse($InfosSujet['cat_reglement']).'</div>'; ?>
<?php if(!empty($InfosSujet['sujet_corbeille'])){ ?>
<div class="UI_errorbox">
	Ce message a été jeté à la corbeille !
	<a href="corbeille-<?php echo $_GET['id']; ?>-0.html?token=<?php echo $_SESSION['token']; ?>">
		Restaurer le sujet
	</a>
</div>
<?php } ?>

<?php echo $SautRapide; ?>

<?php
//Affichage des boutons pour répondre et créer un nouveau sujet
if(verifier('creer_sujets', $InfosSujet['sujet_forum_id']) OR verifier('repondre_sujets', $InfosSujet['sujet_forum_id'])){ ?>
<p class="reponse_ajout_sujet">
	<?php
	//Début Vérification Anti-UP
	if(empty($InfosSujet['dernier_message_auteur']))
	{
		$InfosSujet['dernier_message_auteur'] = $InfosSujet['sujet_auteur'];
		$InfosSujet['dernier_message_date'] = $InfosSujet['sujet_date'];
	}
	$InfosSujet['dernier_message_date'] = strtotime($InfosSujet['dernier_message_date']);
	$timestamp_actuel = time();
	if(verifier('anti_up', $InfosSujet['sujet_forum_id']) != 0)
		$secondes = verifier('anti_up', $InfosSujet['sujet_forum_id'])*3600 - ($timestamp_actuel-$InfosSujet['dernier_message_date']);
	else
		$secondes = 0;
	$AntiUPActif = (($secondes > 0) AND $InfosSujet['dernier_message_auteur'] == $_SESSION['id']);
	//Fin Vérification Anti-UP

	//Si le sujet est fermé et que l'on peut répondre aux sujets fermés, on affiche le bouton "fermé" avec un lien ou alors le bouton "anti-UP"
	if($InfosSujet['sujet_ferme'] AND verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
		if($AntiUPActif)
		{
		?>
		<img src="/bundles/zcoforum/img/antiUP.png" alt="antiUP" title="Vous devez attendre <?php echo verifier('anti_up', $InfosSujet['sujet_forum_id']); ?> heures pour remonter un sujet." />&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="<?php echo 'repondre-'.$_GET['id']; ?>.html"><img src="/bundles/zcoforum/img/ferme.png" alt="Répondre" title="Répondre à ce sujet" /></a>&nbsp;
		<?php
		}
	}
	//Si le sujet est fermé et que l'on n'est pas admin, on affiche le bouton "fermé", mais sans lien.
	elseif($InfosSujet['sujet_ferme'] AND !verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
	<img src="/bundles/zcoforum/img/ferme.png" alt="Fermé" title="Sujet fermé" />
	<?php
	}
	//Si le sujet n'est pas fermé, on affiche le bouton "répondre", ou le bouton Anti-UP
	elseif(verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
		if($AntiUPActif)
		{
		?>
		<img src="/bundles/zcoforum/img/antiUP.png" alt="antiUP" title="Vous devez attendre <?php echo verifier('anti_up', $InfosSujet['sujet_forum_id']); ?> heures pour remonter un sujet." />&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="<?php echo 'repondre-'.$_GET['id']; ?>.html">
			<img src="/bundles/zcoforum/img/repondre.png" alt="Répondre" title="Répondre à ce sujet" />
		</a>&nbsp;
		<?php
		}
	}

	if(verifier('creer_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
	<a href="nouveau-<?php echo $InfosSujet['sujet_forum_id']; ?>.html<?php if($InfosSujet['sujet_corbeille']) echo '?trash=1'; ?>">
		<img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" />
	</a>
	<?php } ?>
</p>
<?php } ?>

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
	include(dirname(__FILE__).'/sondage.html.php');
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
			<td colspan="2">
				<?php echo $view->render('ZcoForumBundle::_liste_connectes.html.php', array('ListerVisiteurs' => $ListerVisiteurs)) ?>
			</td>
		</tr>
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
				<?php if($valeur['utilisateur_absent']==1) { ?>
					<span class="commandes_textuelles"><a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html#absence" rel="nofollow"><img src="/bundles/zcooptions/img/gerer_absence.png" alt="Absent" title="Membre absent. Fin : <?php
					if(is_null($valeur['utilisateur_fin_absence']))
					{
						echo 'indéterminée';
					}
					else
					{
						echo dateformat($valeur['utilisateur_fin_absence'], DATE, MINUSCULE);
					} ?>" /></a></span>
				<?php } ?>
				<img src="/pix.gif" class="fff status_<?php echo str_replace('.png', '', $valeur['statut_connecte']); ?>" alt="<?php echo $valeur['statut_connecte_label']; ?>" title="<?php echo $valeur['statut_connecte_label']; ?>" />
				<?php if(!empty($valeur['auteur_groupe'])) { ?>
				<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;" rel="nofollow">
				<?php } echo htmlspecialchars($valeur['auteur_message_pseudo']); if(!empty($valeur['auteur_groupe'])) { ?>
				</a>
				<?php } ?>
			</td>
			<td class="dates">
				<?php
				//Indiquer le message comme ayant aidé
				if (	!( // Pas le premier message du sujet
						$_GET['p'] == 1 &&
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
						<a href="reponse-help-<?php echo $_GET['id']; ?>-<?php echo $valeur['message_id']; ?>.html?help_souhaite=0&amp;token=<?php echo $_SESSION['token']; ?>">
							<img src="/pix.gif" class="fff delete" alt="Indiquer que cette réponse de m'a pas aidé" class="icone_commande" />
							Cette réponse ne m'a pas aidé
						</a>
					</span>
					<?php } else{ ?>
					<span class="commandes_textuelles">
						<a href="reponse-help-<?php echo $_GET['id']; ?>-<?php echo $valeur['message_id']; ?>-1.html?help_souhaite=1&amp;token=<?php echo $_SESSION['token']; ?>">
							<img src="/pix.gif" class="fff accept" alt="Indiquer que cette réponse m'a aidé" class="icone_commande" />
							Cette réponse m'a aidé
						</a>
					</span>
					<?php
					}
				}
				//Date d'envoi du message
				?>
				<span id="m<?php echo $valeur['message_id'];?>"><a href="sujet-<?php echo $_GET['id'].'-'.$valeur['message_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html'; ?>" rel="nofollow">#</a></span>
				Posté <?php echo dateformat($valeur['message_date'], MINUSCULE); ?>
				<?php
				//Citation du message
				if
				(
					(
						(
							verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND $InfosSujet['sujet_ferme']
						)
						OR
						(
							verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) AND !$InfosSujet['sujet_ferme']
						)
					)
					AND !$InfosSujet['sujet_corbeille']
				)
				{
				?>
				<a href="<?php echo 'repondre-'.$_GET['id'].'-'.$valeur['message_id']; ?>.html">
					<img src="/pix.gif" class="fff comment" alt="Citer" title="Citer" />
				</a>
				<?php
				}
				//Envoi d'un MP
				if(
					$_SESSION['id'] != $valeur['message_auteur']
					AND verifier('connecte')
					AND ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1)
				)
				{
				?>
				<a href="/mp/nouveau-<?php echo $valeur['message_auteur']; ?>.html"><img src="/pix.gif" class="fff email" alt="MP" title="Envoyer un message privé" /></a>
				<?php
				}

								//Marquer comme dernier message lu
								if(verifier('connecte') && $InfosSujet['lunonlu_message_id'] != $valeur['message_id'])
								{
								?>
								<a href="<?php echo 'marquer-dernier-message-lu-'.$valeur['message_id']; ?>.html?token=<?php echo $_SESSION['token']; ?>">
									<img src="/pix.gif" class="fff lightbulb_off_add" alt="Marquer comme dernier message lu" title="Marquer comme dernier message lu" />
								</a>
								<?php
								}

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
				<a href="<?php echo 'editer-'.$valeur['message_id']; ?>.html">
					<img src="/pix.gif" class="fff pencil" alt="Éditer" title="Éditer" />
				</a>
				<?php
				}
				//Suppression du sujet par le premier message
				if($valeur['message_id'] == $InfosSujet['sujet_premier_message'] && verifier('suppr_sujets', $InfosSujet['sujet_forum_id']))
				{
				?>
				<a href="<?php echo 'supprimer-sujet-'.$_GET['id']; ?>.html">
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
				<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html" rel="nofollow"><img src="/uploads/avatars/<?php echo $valeur['auteur_avatar']; ?>" alt="<?php echo 'Avatar de '.htmlspecialchars($valeur['auteur_message_pseudo']); ?>" /></a>
				<br />

				<?php }	if(verifier('voir_nb_messages')){ ?>
				Messages : <?php echo $valeur['utilisateur_forum_messages']; ?><br />

				<?php } echo $view->get('messages')->afficherGroupe($valeur).'<br/>';

				if(!empty($valeur['utilisateur_titre']))
				{
					echo htmlspecialchars($valeur['utilisateur_titre']).'<br />';
				} if(verifier('sanctionner')){ ?>
				<br /><a href="<?php echo $view['router']->path('zco_user_admin_punish', array('id' => htmlspecialchars($valeur['message_auteur']))) ?>">
					Sanctionner
				</a>
				(<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html#sanctions"><?php echo $valeur['utilisateur_nb_sanctions']; ?></a>)
				<?php } elseif(verifier('voir_sanctions') && $valeur['utilisateur_nb_sanctions'] > 0){ ?>
				<br /><a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html#sanctions">Sanction(s) : <?php echo $valeur['utilisateur_nb_sanctions']; ?></a>

				<?php } if(verifier('ips_analyser') && !empty($valeur['message_ip'])){ ?>
				<br /><br />IP : <a href="<?php echo $view['router']->path('zco_user_ips_analyze', ['ip' => long2ip($valeur['message_ip'])]) ?>"><?php echo long2ip($valeur['message_ip']); ?></a>
				<?php } ?>
			</td>
			<td class="message<?php if($valeur['message_help']) echo ' bonne_reponse'; ?>">
				<div class="msgbox">
					<?php
					//En cas de reprise du dernier message
					if($numero_message == 1 AND $_GET['p'] > 1 AND $_GET['p'] <= $NombreDePages)
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

					<?php /*if(!empty($Tags) && $_GET['p'] == 1 && $clef == 0){ ?>
					<div class="tags">
						<span class="label">Mots-clés :</span>
						<?php foreach($Tags as $tag){ ?>
						<a href="/tags/tag-<?php echo $tag['tag_id']; ?>-<?php echo rewrite($tag['tag_nom']); ?>.html" class="tag">
							<?php echo htmlspecialchars($tag['tag_nom']); ?>
						</a>
						<?php } ?>
					</div>
					<?php }*/ ?>

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

<p class="centre">
<strong>
<?php
if($InfosSujet['sujet_corbeille'])
{
?>
Retour <a href="<?php echo FormateURLCategorie($InfosSujet['sujet_forum_id']); ?>?trash=1">à la corbeille du forum <em><?php echo htmlspecialchars($InfosForum['cat_nom']); ?></em></a> ou <a href="index.html?trash=1">à l'accueil de la corbeille</a>
<?php
}
else
{
?>
Retour <a href="<?php echo FormateURLCategorie($InfosSujet['sujet_forum_id']); ?>">au forum <em><?php echo htmlspecialchars($InfosForum['cat_nom']); ?></em></a>
ou <a href="/forum/">à la liste des forums</a>
<?php
}
?>
</strong>
</p>

<?php
if(verifier('creer_sujets', $InfosSujet['sujet_forum_id']) OR verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
{
?>
<p class="reponse_ajout_sujet">
<?php
}

	//Si le sujet est fermé et que l'on peut répondre aux sujets fermés, on affiche le bouton "fermé" avec un lien ou alors le bouton "anti-UP"
	if($InfosSujet['sujet_ferme'] AND verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
		if($AntiUPActif)
		{
		?>
		<img src="/bundles/zcoforum/img/antiUP.png" alt="antiUP" title="Vous devez attendre <?php echo DUREE_PROTECTION_ANTI_UP; ?> heures pour remonter un sujet." />&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="<?php echo 'repondre-'.$_GET['id']; ?>.html"><img src="/bundles/zcoforum/img/ferme.png" alt="Répondre" title="Répondre à ce sujet" /></a>&nbsp;
		<?php
		}
	}
	//Si le sujet est fermé et que l'on ne peut pas répondre aux sujets fermés, on affiche le bouton "fermé", mais sans lien.
	elseif($InfosSujet['sujet_ferme'] AND !verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
	<img src="/bundles/zcoforum/img/ferme.png" alt="Fermé" title="Sujet fermé" />
	<?php
	}
	//Si le sujet n'est pas fermé, on affiche le bouton "répondre", ou le bouton Anti-UP
	elseif(verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
	{
		if($AntiUPActif)
		{
		?>
		<img src="/bundles/zcoforum/img/antiUP.png" alt="antiUP" title="Vous devez attendre <?php echo verifier('anti_up', $InfosSujet['sujet_forum_id']); ?> heures pour remonter un sujet." />&nbsp;
		<?php
		}
		else
		{
		?>
		<a href="<?php echo 'repondre-'.$_GET['id']; ?>.html"><img src="/bundles/zcoforum/img/repondre.png" alt="Répondre" title="Répondre à ce sujet" /></a>&nbsp;
		<?php
		}
	}

	if(verifier('creer_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		<a href="nouveau-<?php echo $InfosSujet['sujet_forum_id']; ?>.html<?php if($InfosSujet['sujet_corbeille']) echo '?trash=1' ?>"><img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" /></a>
	<?php
	}

if(verifier('creer_sujets', $InfosSujet['sujet_forum_id']) OR verifier('repondre_sujets', $InfosSujet['sujet_forum_id']))
{
?>
</p>
<?php
}

echo $SautRapide;

$ReponseRapide = '
<div id="reponse_rapide">
<form action="repondre-'.$_GET['id'].'.html" method="post">
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
		if(!$AntiUPActif)
		{
			$view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/save.js');
			echo $ReponseRapide;
		}
	}
	elseif(verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) AND !$InfosSujet['sujet_ferme'])
	{
		if(!$AntiUPActif)
		{
			$view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/save.js');
			echo $ReponseRapide;
		}
	}
}

if($afficher_options)
{
	include(dirname(__FILE__).'/_options_bas_sujet.html.php');
}
?>
