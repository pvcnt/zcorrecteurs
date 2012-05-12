<?php $view->extend('::layouts/default.html.php') ?>

<h1>Valider un billet</h1>

<fieldset>
	<legend>Valider un billet</legend>
	<form method="post" action="">
		<p>
			Êtes-vous sûr de vouloir valider ce billet
			ce billet ayant pour titre
			<strong><a href="/blog/billet-<?php echo $InfosBillet['blog_id']; ?>.html">
				<?php echo htmlspecialchars($InfosBillet['version_titre']); ?></a>
			</strong> ?
		</p>

		<?php if(!is_null($InfosBillet['blog_date_publication']) && $InfosBillet['blog_date_publication'] != '0000-00-00 00:00:00'){ ?>
		<p>
			<input type="checkbox" name="conserver_date_pub" id="conserver_date_pub" />
			<label for="conserver_date_pub" class="nofloat">
				Conserver la date de publication indiquée (<strong><?php echo dateformat($InfosBillet['blog_date_publication'], MINUSCULE); ?></strong>).
			</label>
		</p>
		<?php } ?>
		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
