<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un message automatique</h1>

<p>
	En modifiant un message automatique, vous pouvez spécifier si celui-ci
	fermera le sujet lorsqu'il est appliqué. Vous préciserez aussi si vous
	le souhaitez un tag, c'est à dire un préfixe qui sera ajouté au sujet
	après ajout du message automatique.
</p>

<fieldset>
	<legend>Modifier un message automatique</legend>
	<form method="post" action="">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($InfosMessage['nom']); ?>" /><br />

		<label for="tag">Tag : </label>
		<input type="text" name="tag" id="tag" size="40" value="<?php echo htmlspecialchars($InfosMessage['tag']); ?>" /><br />

		<label for="ferme">Ferme le sujet : </label>
		<input type="checkbox" name="ferme" id="ferme"<?php echo $InfosMessage['ferme'] ? ' checked="checked"' : ''; ?> /><br />

		<label for="resolu">Met le sujet en résolu : </label>
		<input type="checkbox" name="resolu" id="resolu"<?php echo $InfosMessage['resolu'] ? ' checked="checked"' : ''; ?> /><br />

		<label for="texte">Texte : </label>
		<?php echo $view->render('::zform.html.php', array('texte' => $InfosMessage['texte'])); ?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

