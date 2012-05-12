<?php $view->extend('::layouts/default.html.php') ?>

<h1>
	<?php echo htmlspecialchars($page['titre']) ?>

	<?php if(verifier('aide_modifier')){ ?>
	<a href="modifier-<?php echo $page['id'] ?>.html">
		<img src="/img/editer.png" alt="Modifier le contenu" />
	</a>
	<?php } if(verifier('aide_supprimer')){ ?>
	<a href="supprimer-<?php echo $page['id'] ?>.html">
		<img src="/img/supprimer.png" alt="Supprimer la page" />
	</a>
	<?php } ?>
</h1>

<p><?php echo $view['messages']->parse($page['contenu'], array(
    'core.anchor_prefix' => $page['id'],
    'files.entity_id' => $page['id'],
    'files.entity_class' => 'Aide',
)) ?></p>

<p class="UI_box centre gras" style="margin-top: 20px;">
	<a href="index.html">Retour à l'accueil du centre d'aide</a>
</p>
