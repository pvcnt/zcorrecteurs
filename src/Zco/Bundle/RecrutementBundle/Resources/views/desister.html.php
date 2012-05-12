<?php $view->extend('::layouts/default.html.php') ?>

<h1>Se désister</h1>

<form method="post" action="">
	<fieldset>
		<legend>Désistement</legend>
		<p>En validant cette action, vous indiquez que vous ne souhaitez plus faire partie de ce recrutement.
		Il vous faudra attendre une prochaine session de recrutement pour postuler de nouveau.
		<strong>Cette action est irréversible.</strong></p>

		<div class="send">
			<input type="submit" name="submit" value="Valider mon désistement" />
		</div>
	</fieldset>
</form>