<?php $view->extend('::layouts/default.html.php') ?>

<?php $view['slots']->start('meta') ?>
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="zcorrecteurs" />
<meta name="twitter:url" content="<?php echo URL_SITE ?>/blog/billet-<?php echo $InfosBillet['blog_id'] ?>-<?php echo rewrite($InfosBillet['version_titre']) ?>.html" />
<meta name="twitter:description" content="<?php echo htmlspecialchars(strip_tags($InfosBillet['version_intro'])) ?>" />
<meta name="twitter:title" content="<?php echo htmlspecialchars($InfosBillet['version_titre']) ?>" />
<meta name="twitter:image" content="<?php echo URL_SITE ?>/<?php echo htmlspecialchars($InfosBillet['blog_image']); ?>" />
<?php $view['slots']->stop() ?>

<h1><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></h1>

<?php if(!empty($InfosBillet['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?></h2>
<?php } ?>

<!-- Liste des catégories -->
<?php if($InfosBillet['blog_etat'] == BLOG_VALIDE){ ?>
<?php echo $view->render('ZcoBlogBundle::_liste_categories.html.php'); ?>
<?php } ?>

<?php if($InfosBillet['blog_etat'] == BLOG_PROPOSE){ ?>
<div class="rmq information">
	Le billet que vous visualisez est <strong>proposé</strong>.
</div>
<?php } elseif($InfosBillet['blog_etat'] == BLOG_BROUILLON){ ?>
<div class="rmq information">
	Le billet que vous visualisez est <strong>en cours de rédaction</strong>.
</div>
<?php } elseif($InfosBillet['blog_etat'] == BLOG_REFUSE){ ?>
<div class="rmq information">
	Le billet que vous visualisez est <strong>refusé</strong>.
</div>
<?php } elseif($InfosBillet['blog_etat'] == BLOG_PREPARATION){ ?>
<div class="rmq information">
	Le billet que vous visualisez est <strong>en cours de préparation</strong>.
</div>
<?php } ?>

<!-- Billet -->
<?php echo $view->render('ZcoBlogBundle::_billet.html.php',
	array(
		'verifier_editer' => $verifier_editer,
		'verifier_devalider' => $verifier_devalider,
		'verifier_supprimer' => $verifier_supprimer,
		'InfosBillet' => $InfosBillet,
		'Auteurs' => $Auteurs,
	)) ?>

<br /><hr />
<h2 id="commentaires">
	<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK || !empty($ListerCommentaires)){ ?>
	<?php echo $CompterCommentaires; ?> commentaire<?php echo pluriel($CompterCommentaires); ?>
	sur ce billet
	<?php } else{ ?>
	Commentaires sur ce billet
	<?php } ?>
</h2>

<p class="reponse_ajout_sujet">
	<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK && verifier('connecte')){ ?>
	<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>.html" title="Ajouter un commentaire">
		<img src="/bundles/zcoforum/img/repondre.png" alt="Ajouter un commentaire" />
	</a>
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE && !verifier('blog_poster_commentaires_fermes')){ ?>
	<img src="/bundles/zcoforum/img/ferme.png" alt="Commentaires désactivés" />
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE && verifier('blog_poster_commentaires_fermes')){ ?>
	<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>.html" title="Ajouter un commentaire">
		<img src="/bundles/zcoforum/img/ferme.png" alt="Ajouter un commentaire" />
	</a>
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_TOPIC){ ?>
	<em>
		<a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_topic']); ?>">
			Continuer la discussion sur le forum
		</a>
	</em>
	<?php } ?>
</p>

<?php if(!empty($ListerCommentaires)){ ?>
<table class="UI_items messages" id="commentaires">
	<thead>
		<tr>
			<td colspan="2">Page :
				<?php foreach($ListePages as $element) echo $element; ?>
			</td>
		</tr>

		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>

	<tfoot>
		<tr><td colspan="2">Page :
				<?php foreach($ListePages as $element) echo $element; ?>
		</td></tr>
	</tfoot>

	<tbody>
		<?php foreach($ListerCommentaires as $clef => $valeur){ ?>
		<tr class="header_message">
			<td class="pseudo_membre">
                <a href="<?php echo $view['router']->path('zco_user_profile', ['id' => $valeur['id_auteur'], 'slug' => rewrite($valeur['pseudo_auteur'])]) ?>">
                    <?php echo htmlspecialchars($valeur['pseudo_auteur']) ?>
                </a>
			</td>

			<td class="dates">
				<span id="m<?php echo $valeur['commentaire_id'];?>">
					<a href="billet-<?php echo $_GET['id']; ?>-<?php echo $valeur['commentaire_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">#</a>
				</span>

				Ajouté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE); ?>
				<?php if(verifier('connecte') && ($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))){ ?>
				<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>-<?php echo $valeur['commentaire_id']; ?>.html"><img src="/bundles/zcoforum/img/citer.png" alt="Citer" title="Citer" /></a>
				<?php }
				if((($valeur['id_auteur'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires')) || verifier('blog_editer_commentaires')) && ($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes'))){ ?>
				<a href="<?php echo $view['router']->path('zco_blog_editComment', ['id' => $valeur['commentaire_id']]) ?>" title="Modifier ce commentaire">
					<img src="/img/editer.png" alt="Modifier" /></a>
				<?php } if(verifier('blog_editer_commentaires') || ($createur == true && in_array($InfosBillet['blog_etat'], array(BLOG_REFUSE, BLOG_BROUILLON)))){ ?>
				<a href="<?php $view['router']->paht('zco_blog_deleteComment', ['id' => $valeur['commentaire_id']]) ?>" title="Supprimer ce commentaire">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
				<?php } ?>
			</td>
		</tr>

		<tr>
			<td class="infos_membre">
				<?php if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />' ; } ?>

				<?php echo $view['messages']->afficherAvatar($valeur, 'avatar_auteur') ?><br/>
				<?php echo $view['messages']->afficherGroupe($valeur) ?><br/>
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

<?php } else{	?>
Aucun commentaire n'a encore été déposé sur ce billet.
<?php if(($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK && verifier('connecte')) || ($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE && verifier('blog_poster_commentaires_fermes'))) echo '<a href="ajouter-commentaire-'.$_GET['id'].'.html">Soyez le premier à en déposer un !</a>'; ?>
<?php }	?>

<?php if (count($ListerCommentaires) > 0){ ?>
<p class="reponse_ajout_sujet">
	<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK && verifier('connecte')){ ?>
	<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>.html" title="Ajouter un commentaire">
		<img src="/bundles/zcoforum/img/repondre.png" alt="Ajouter un commentaire" />
	</a>
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE && !verifier('blog_poster_commentaires_fermes')){ ?>
	<img src="/bundles/zcoforum/img/ferme.png" alt="Commentaires désactivés" />
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE && verifier('blog_poster_commentaires_fermes')){ ?>
	<a href="ajouter-commentaire-<?php echo $_GET['id']; ?>.html" title="Ajouter un commentaire">
		<img src="/bundles/zcoforum/img/ferme.png" alt="Ajouter un commentaire" />
	</a>
	<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_TOPIC){ ?>
	<em>
		<a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_topic']); ?>">
			Continuer la discussion sur le forum
		</a>
	</em>
	<?php } ?>
</p>
<?php } ?>

<?php if (verifier('blog_choisir_comms') && $InfosBillet['blog_commentaires'] != COMMENTAIRES_TOPIC) { ?>
<fieldset id="panel_moderation">
	<legend>Modération massive des commentaires</legend>
	<ul>
		<?php if($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK){ ?>
		<li>
			<img src="/bundles/zcoforum/img/cadenas.png" alt="" />
			<a href="?fermer=1">Fermer les commentaires</a>
		</li>
		<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_NONE){ ?>
		<li>
			<img src="/bundles/zcoforum/img/cadenas.png" alt="" />
			<a href="?fermer=0">Ouvrir les commentaires</a>
		</li>
		<?php } ?>
	</ul>
</fieldset>
<?php } ?>
