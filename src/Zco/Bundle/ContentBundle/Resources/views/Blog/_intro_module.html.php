<div class="blog" style="min-height: 130px;">
	<h5 class="title">
		<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>"
			title="Écrit par <?php foreach($Auteurs as $a) { echo htmlspecialchars($a['utilisateur_pseudo']).', '; } echo dateformat($InfosBillet['blog_etat'] == BLOG_VALIDE ? $InfosBillet['blog_date_publication'] : $InfosBillet['blog_date'], MINUSCULE); ?>">
				<?php echo htmlspecialchars($InfosBillet['version_titre']); ?>
			</a>
	</h5>

	<div class="zcode">
		<img class="image flot_<?php echo (isset($cote) ? $cote : (($nb % 2) == 0 ? 'gauche' : 'droite')); ?>" src="/<?php echo htmlspecialchars($InfosBillet['blog_image']); ?>" alt="Logo du billet" />
		<?php echo $view['messages']->parse($InfosBillet['version_intro'], array(
		    'core.anchor_prefix' => $InfosBillet['blog_id'],
		    'files.entity_id' => $InfosBillet['blog_id'],
		    'files.entity_class' => 'Blog',
			'files.part' => 1,
		)); ?><br style="clear:both;" />
	</div>

	<div class="forum">
		<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>">
			Lire la suite…
		</a> —
		<?php if(!empty($InfosBillet['lunonlu_id_commentaire']) && verifier('connecte')){ ?>
		<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre']), 'c' => $InfosBillet['lunonlu_id_commentaire']]) ?>#m<?php echo $InfosBillet['lunonlu_id_commentaire']; ?>"
           title="Aller au dernier message lu">
			<img src="/bundles/zcocontent/img/fleche.png" alt="Dernier message lu" />
		</a>
		<?php } ?>
		<a href="<?php echo $view['router']->path('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]) ?>#commentaires">
			<?php echo $InfosBillet['blog_nb_commentaires']; ?> commentaire<?php echo pluriel($InfosBillet['blog_nb_commentaires']); ?>
		</a>
	</div>
</div>
