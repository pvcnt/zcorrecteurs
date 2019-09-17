<div class="blog">
	<div class="info">
		<span class="moderation">
			<?php if($credentials->canEdit()){ ?>
			<a href="<?php echo $view['router']->path('zco_blog_manage', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>" title="Modifier le billet">
				<img src="/img/editer.png" alt="Modifier" />
			</a>
			<?php } if($credentials->canUnpublish()){ ?>
			<a href="<?php echo $view['router']->path('zco_blog_unpublish', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>" title="Mettre le billet hors ligne">
				<img src="/bundles/zcoblog/img/refuser.png" alt="Dévalider" />
			</a>
			<?php } if($credentials->canDelete()){ ?>
			<a href="<?php echo $view['router']->path('zco_blog_unpublish', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>" title="Supprimer le billet">
				<img src="/img/supprimer.png" alt="Supprimer" />
			</a>
			<?php } ?>
		</span>

		<p class="categorie">
			Catégorie :
			<a href="<?php echo $view['router']->path('zco_blog_index', ['filtre' => $InfosBillet['cat_id']]) ?>"
			    title="Tous les billets de la catégorie <?php echo htmlspecialchars($InfosBillet['cat_nom']); ?>">
				<?php echo htmlspecialchars($InfosBillet['cat_nom']); ?>
			</a>
		</p>

		<p class="auteur">
			Écrit par
			<?php foreach($Auteurs as $a){ ?>
			<?php if ($a['auteur_statut'] > 1){ ?>
			<a href="/membres/profil-<?php echo $a['utilisateur_id']; ?>-<?php echo rewrite($a['utilisateur_pseudo']); ?>.html">
				<?php echo htmlspecialchars($a['utilisateur_pseudo']); ?></a>,
			<?php } ?>
			<?php } ?>

			<?php echo dateformat($InfosBillet['blog_etat'] == BLOG_VALIDE ? $InfosBillet['blog_date_publication'] : $InfosBillet['blog_date'], MINUSCULE); ?>
			|	<em><a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>#commentaires">
					Commenter ce billet
					(<?php echo $InfosBillet['blog_nb_commentaires']; ?> commentaire<?php echo pluriel($InfosBillet['blog_nb_commentaires']); ?>)
				</a></em>
		</p>
	</div>

	<div class="zcode">
		<?php echo $view['messages']->parse($InfosBillet['version_intro'], array(
		    'core.anchor_prefix' => $InfosBillet['blog_id'],
		    'files.entity_id' => $InfosBillet['blog_id'],
		    'files.entity_class' => 'Blog',
			'files.part' => 1,
		)); ?>
		<br /><br />

		<?php echo $view['messages']->parse($InfosBillet['version_texte'], array(
		    'core.anchor_prefix' => $InfosBillet['blog_id'],
		    'files.entity_id' => $InfosBillet['blog_id'],
		    'files.entity_class' => 'Blog',
			'files.part' => 2,
		)); ?>

		<?php if(!empty($InfosBillet['blog_lien_url'])){ ?>
		<p class="UI_box">
			<strong>Ressource à visiter en ligne :</strong>
			<a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_url']); ?>">
				<?php echo !empty($InfosBillet['blog_lien_nom']) ?
				htmlspecialchars($InfosBillet['blog_lien_nom']) :
				htmlspecialchars($InfosBillet['blog_lien_url']); ?>
			</a>
		</p>
		<?php } ?>
	</div>

	<div class="forum">
		<span class="flot_droite">
			<?php if (!empty($InfosBillet['blog_nb_vues'])){ ?>
			<?php echo $view['humanize']->numberformat($InfosBillet['blog_nb_vues'], 0) ?> visualisation<?php echo pluriel($InfosBillet['blog_nb_vues']) ?> |
			<?php } ?>
			<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>#commentaires">
				Commenter ce billet
			</a> |
			<a href="https://twitter.com/share?text=<?php echo urlencode('Venez découvrir cet article des @zCorrecteurs : ') ?>&url=<?php echo $view['router']->url('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>" class="italique">
                <img src="/img/oiseau_16px.png" alt="Twitter" />
                Partager sur Twitter
            </a> |
			<a href="#header">Remonter</a>
		</span>&nbsp;
	</div>
</div>
