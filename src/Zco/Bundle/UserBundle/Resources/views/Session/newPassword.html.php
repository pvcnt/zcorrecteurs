<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Demande d'un nouveau mot de passe</h1>

<form method="post" action="<?php echo $view['router']->generate('zco_user_session_newPassword') ?>" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label for="email" class="control-label">Votre adresse courriel</label>
			<div class="controls">
				<input type="text" name="email" id="email" />
			</div>
		</div>
		<input type="submit" class="btn btn-primary" value="Demander le nouveau mot de passe" />
		<a href="<?php echo $view['router']->generate('zco_user_session_login') ?>" class="btn">Retour au formulaire de connexion</a>
	</fieldset>
</form>