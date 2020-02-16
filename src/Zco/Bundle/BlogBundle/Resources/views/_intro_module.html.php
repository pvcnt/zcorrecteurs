<div class="blog" style="min-height: 130px;">
	<h5 class="title">
		<a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html"
			title="Écrit par <?php foreach($Auteurs as $a) { echo htmlspecialchars($a['utilisateur_pseudo']).', '; } echo dateformat($InfosBillet['blog_etat'] == BLOG_VALIDE ? $InfosBillet['blog_date_publication'] : $InfosBillet['blog_date'], MINUSCULE); ?>">
				<?php echo htmlspecialchars($InfosBillet['version_titre']); ?>
			</a>
	</h5>

	<div class="zcode">
		<img class="image flot_<?php echo (isset($cote) ? $cote : (($nb % 2) == 0 ? 'gauche' : 'droite')); ?>" src="<?php echo $this['messages']->blobUrl($InfosBillet['blog_image']) ?>" alt="Logo du billet" />
		<?php echo $view['messages']->parse($InfosBillet['version_intro'], array(
		    'core.anchor_prefix' => $InfosBillet['blog_id'],
		    'files.entity_id' => $InfosBillet['blog_id'],
		    'files.entity_class' => 'Blog',
			'files.part' => 1,
		)); ?><br style="clear:both;" />
	</div>

	<div class="forum">
		<a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html">
			Lire la suite…
		</a>

		<?php if(!empty($InfosBillet['blog_lien_topic']) && $InfosBillet['blog_commentaires'] == COMMENTAIRES_TOPIC){ ?>
		—	<a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_topic']); ?>">
				Continuer la discussion sur le forum
			</a>
		<?php } elseif($InfosBillet['blog_commentaires'] == COMMENTAIRES_OK){ ?> —
		<?php if(!empty($InfosBillet['lunonlu_id_commentaire']) && verifier('connecte')){ ?>
		<a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo $InfosBillet['lunonlu_id_commentaire']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html#m<?php echo $InfosBillet['lunonlu_id_commentaire']; ?>" title="Aller au dernier message lu">
			<img src="/bundles/zcoforum/img/fleche.png" alt="Dernier message lu" />
		</a>
		<?php } ?>

		<a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>-<?php echo rewrite($InfosBillet['version_titre']); ?>.html#commentaires">
			<?php echo $InfosBillet['blog_nb_commentaires']; ?> commentaire<?php echo pluriel($InfosBillet['blog_nb_commentaires']); ?>
		</a>
		<?php } ?>
	</div>
</div>
