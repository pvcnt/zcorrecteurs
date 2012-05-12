<div class="blog">
	<h2 class="title">
		<a href="billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">
			<?php echo htmlspecialchars($InfosBillet['version_titre']); ?>
		</a>
	</h2>

	<div class="info">
		<span class="moderation">
			<?php if((in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && ($redacteur == true || verifier('blog_editer_brouillons'))) ||
			($InfosBillet['blog_etat'] == BLOG_PREPARATION && verifier('blog_editer_preparation')) ||
			($InfosBillet['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))){ ?>
			<a href="admin-billet-<?php echo $InfosBillet['blog_id']; ?>.html" title="Modifier le billet">
				<img src="/img/editer.png" alt="Modifier" />
			</a>
			<?php } if(verifier('blog_devalider') && in_array($InfosBillet['blog_etat'], array(BLOG_VALIDE, BLOG_PREPARATION))){ ?>
			<a href="devalider-<?php echo $InfosBillet['blog_id']; ?>.html" title="Mettre le billet hors ligne">
				<img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" />
			</a>
			<?php } if(verifier('blog_supprimer')){ ?>
			<a href="supprimer-<?php echo $InfosBillet['blog_id']; ?>.html" title="Supprimer le billet">
				<img src="/img/supprimer.png" alt="Supprimer" />
			</a>
			<?php } ?>
		</span>

		<p class="categorie">
			Catégorie :
			<a href="categorie-<?php echo $InfosBillet['cat_id']; ?>-<?php echo rewrite($InfosBillet['cat_nom']); ?>.html" title="Tous les billets de la catégorie « <?php echo htmlspecialchars($InfosBillet['cat_nom']); ?> »">
				<?php echo htmlspecialchars($InfosBillet['cat_nom']); ?>
			</a>
		</p>

		<p class="auteur">
			Écrit par
			<?php foreach($Auteurs as $a){ ?>
			<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($a['utilisateur_pseudo']); ?></a>,
			<?php } ?>

			<?php echo dateformat($InfosBillet['blog_etat'] == BLOG_VALIDE ? $InfosBillet['blog_date_publication'] : $InfosBillet['blog_date'], MINUSCULE); ?>

			<?php if(!empty($InfosBillet['blog_lien_topic']) && $InfosBillet['blog_commentaires'] == COMMENTAIRES_TOPIC){ ?>
			— <em><a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_topic']); ?>">
				Continuer la discussion sur le forum
			</a></em>
			<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK){ ?>
			— <em>
				<?php if(!empty($InfosBillet['lunonlu_id_commentaire']) && verifier('connecte')){ ?>
				<a href="billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo $InfosBillet['lunonlu_id_commentaire']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html" title="Aller au dernier message lu">
					<img src="/bundles/zcoforum/img/fleche.png" alt="Dernier message lu" />
				</a>
				<?php } ?>

				<a href="billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html#commentaires">
					<?php echo $InfosBillet['blog_nb_commentaires']; ?> commentaire<?php echo pluriel($InfosBillet['blog_nb_commentaires']); ?>
				</a>
			</em>
			<?php } ?>
		</p>
	</div>

	<div class="zcode" style="min-height: 100px;">
		<img class="image flot_gauche" src="<?php echo '/'.htmlspecialchars($InfosBillet['blog_image']); ?>" alt="Logo du billet" />
		<?php echo $view['messages']->parse($InfosBillet['version_intro'], array(
		    'core.anchor_prefix' => $InfosBillet['blog_id'],
		    'files.entity_id' => $InfosBillet['blog_id'],
		    'files.entity_class' => 'Blog',
			'files.part' => 1,
		)); ?>
	</div>

	<div class="forum">
		<a href="billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">
			Lire la suite…
		</a> —
		<a href="#header">Remonter</a>
	</div>
</div>
