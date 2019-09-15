<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer une dictée</h1>

<fieldset>
	<legend>Supprimer une dictée</legend>
	<form method="post" action="">
		<?php if($Dictee->etat == DICTEE_VALIDEE): ?>
			<p class="rmq attention">Cette dictée est en ligne.</p>
		<?php endif; ?>
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer cette dictée,
			dont le titre est
			<strong><a href="<?php echo $url; ?>">
				<?php echo htmlspecialchars($Dictee->titre); ?>
			</a></strong> ?
		</p>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
		</p>
	</form>
</fieldset>
