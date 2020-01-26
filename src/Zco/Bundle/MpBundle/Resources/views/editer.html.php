<?php $view->extend('::layouts/default.html.php') ?>

<h1>Éditer une réponse</h1>
<fieldset>
<legend>Édition d'une réponse</legend>
<form action="" method="post">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<?php echo $view->render('::zform.html.php', array('texte' => $InfoMessage['mp_message_texte'])); ?>

		<div class="cleaner">&nbsp;</div>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

