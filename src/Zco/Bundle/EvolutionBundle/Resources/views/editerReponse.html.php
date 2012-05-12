<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Modifier une réponse</h1>
<h2><?php echo $titre; ?></h2>

<form method="post" action="">
	<fieldset>
		<legend>Modifier une réponse</legend>
		<div class="send">
			<input type="submit" name="submit" value="Envoyer" />
		</div>

		<label for="texte">Contenu de la réponse :</label>
		<?php echo $view->render('::zform.html.php', array('texte' => $InfosReponse['version_commentaire'])); ?>

		<div class="send">
			<input type="submit" name="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>
