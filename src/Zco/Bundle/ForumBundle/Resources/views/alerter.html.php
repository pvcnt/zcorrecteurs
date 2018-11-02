<?php $view->extend('::layouts/default.html.php') ?>

<h1>Alerter les modérateurs</h1>

<p>Vous voulez alerter les modérateurs pour le sujet suivant : <strong><a href="sujet-<?php echo $_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']); ?>.html"><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></a></strong>.<br />
Merci d'indiquer la raison ci-dessous.</p>

<form action="" method="post">
	<fieldset>
		<legend>Alerter les modérateurs</legend>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="2" />
		</div>

		<label for="texte">Raison(s) de votre alerte : </label>
		<?php echo $view->render('::zform.html.php', array(
			'upload_utiliser_element' => true, 
			'upload_id_formulaire' => $_GET['id'],
		)) ?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="3" />
		</div>
	</fieldset>
</form>