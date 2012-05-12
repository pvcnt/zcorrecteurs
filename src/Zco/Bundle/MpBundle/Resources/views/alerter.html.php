<?php $view->extend('::layouts/default.html.php') ?>

<h1>Alerter les modérateurs</h1>
<p>
	Vous voulez alerter les modérateurs pour le MP
	<a href="lire-<?php echo $_GET['id'];?>.html"><strong><?php echo htmlspecialchars($InfoMP['mp_titre']); ?></strong></a>.
	Merci d'en indiquer la raison ci-dessous.
</p>

<fieldset>
<legend>Alerter les modérateurs</legend>
<form action="" method="post">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<?php echo $view->render('::zform.html.php'); ?>

		<div class="cleaner">&nbsp;</div>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>
<p class="centre">Retour au MP <a href="lire-<?php echo $_GET['id'];?>.html"><strong><?php echo htmlspecialchars($InfoMP['mp_titre']); ?></strong></a></p>
