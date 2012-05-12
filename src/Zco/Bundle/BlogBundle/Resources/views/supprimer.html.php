<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un billet</h1>

<fieldset>
	<legend>Supprimer un billet</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer ce billet de <strong><a href="/membres/profil-<?php echo $InfosBillet['utilisateur_id']; ?>-<?php echo rewrite($InfosBillet['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosBillet['utilisateur_pseudo']); ?></a></strong>
			ayant pour titre <strong><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></strong> ?
		</p>

		<?php if($InfosBillet['blog_etat'] == BLOG_PROPOSE){ ?>
		<p class="rouge centre">Attention : ce billet est <strong>proposé</strong>. Vous devriez donc l'accepter ou le refuser,
		mais pas le supprimer, sauf si vous savez ce que vous faites.</p>
		<?php } ?>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
