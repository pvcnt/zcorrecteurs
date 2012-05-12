<?php $view->extend('::layouts/default.html.php') ?>

<h1>Inscrire un membre</h1>

<p>
	La fonction d'inscription est utile pour inscrire un membre ne pouvant
	pas s'inscrire seul, à cause du captcha par exemple. Son compte est
	automatiquement validé et il reçoit un mail avec son mot de passe à
	l'adresse indiquée.
</p>

<form method="post" action="">
	<fieldset>
		<legend>Inscrire un membre</legend>
		<label for="pseudo">Pseudo :</label>
		<input type="text" name="pseudo" id="pseudo" size="40" /><br />

		<label for="mail">Adresse mail :</label>
		<input type="text" name="mail" id="mail" size="40" />

		<div class="send">
			<input type="submit" value="Inscrire" />
		</div>
	</fieldset>
</form>