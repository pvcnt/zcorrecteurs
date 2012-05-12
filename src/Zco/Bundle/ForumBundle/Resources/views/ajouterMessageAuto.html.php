<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un message automatique</h1>

<p>En ajoutant un message automatique, vous pouvez spécifier si celui-ci fermera le sujet lorsqu'il est appliqué. Vous préciserez aussi si vous le souhaitez un tag, c'est à dire un préfixe qui sera ajouté au sujet après ajout du message automatique.</p>

<fieldset>
	<legend>Ajouter un message automatique</legend>
	<form method="post" action="">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" /><br />

		<label for="tag">Tag : </label>
		<input type="text" name="tag" id="tag" size="40" /><br />

		<label for="ferme">Ferme le sujet : </label>
		<input type="checkbox" name="ferme" id="ferme" /><br />

		<label for="resolu">Met le sujet en résolu : </label>
		<input type="checkbox" name="resolu" id="resolu" /><br />

		<label for="texte">Texte : </label>
		<?php echo $view->render('::zform.html.php'); ?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

