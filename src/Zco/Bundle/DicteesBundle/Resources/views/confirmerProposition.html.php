<?php $view->extend('::layouts/default.html.php') ?>

<h1>Proposer une dictée</h1>

<fieldset>
	<legend>Proposer une dictée</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir proposer cette dictée,
			dont le titre est
			<strong><a href="<?php echo $url; ?>">
				<?php echo htmlspecialchars($Dictee->titre); ?>
			</a></strong> ?<br />
			En cliquant sur Oui, votre dictée sera envoyée aux administrateurs, et
			vous recevrez un message privé quand votre dictée aura été examinée.
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
		</p>
	</form>
</fieldset>
