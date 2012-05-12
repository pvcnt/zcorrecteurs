<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>

<form method="post" action="">
	<fieldset>
		<legend>Modifier les commentaires</legend>
		<div class="send">
			<input type="submit" value="Mettre à jour !" name="maj" />
		</div>

		Commentaires à l'auteur :<br />
		<?php echo $view->render('::zform.html.php', array('id' => 'comm', 'texte' => $commentaire)) ?>

		<div class="cleaner">&nbsp;</div>
		<div class="cleaner">&nbsp;</div>

		Commentaires au validateur :<br />
		<?php echo $view->render('::zform.html.php', array('id' => 'comm_valido', 'texte' => $commentaire_valido)) ?>

		<div class="cleaner">&nbsp;</div>
		<div class="cleaner">&nbsp;</div>

		Commentaires aux zCorrecteurs :<br />
		<?php echo $view->render('::zform.html.php', array('id' => 'comm2', 'texte' => $commentaire2)) ?>

		<div class="cleaner">&nbsp;</div>
		<div class="cleaner">&nbsp;</div>

		<label for="confidentialite">Cacher mon pseudo à l'auteur : </label>
		<input type="checkbox" name="confidentialite" id="confidentialite" <?php if($confidentialite) echo 'checked="checked" '; ?> />

		<div class="send">
			<input type="submit" value="Mettre à jour !" name="maj" />
		</div>
	</fieldset>
</form>
