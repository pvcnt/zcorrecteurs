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

		<?php if($InfoMessage['mp_crypte']) { ?>
		<p>
			<label for="crypter" title="Si le destinataire a renseigné une clé PGP, s'en servir pour chiffrer le message.">Chiffrer le message :</label>
			<input type="checkbox" name="crypter" id="crypter" />
		</p>
		<?php } ?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

