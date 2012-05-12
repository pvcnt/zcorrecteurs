<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier le mot de passe</h1>

<form action="" method="post">
	<fieldset>
		<legend>Modifier le mot de passe</legend>
		<?php if($_GET['id'] == $_SESSION['id']){ ?>
		<label for="ancien_mdp">Ancien mot de passe :</label>
		<input type="password" name="ancien_mdp" id="ancien_mdp" tabindex="1" /><br />
		<?php } ?>
		<label for="nouveau_mdp">Nouveau mot de passe :</label>
		<input type="password" name="nouveau_mdp" id="nouveau_mdp"  tabindex="2" /><br />

		<label for="confirm_nouveau_mdp">Confirmer mot de passe :</label>
		<input type="password" name="confirm_nouveau_mdp" id="confirm_nouveau_mdp" tabindex="3" /><br />

		<input type="submit" name="submit" value="Envoyer" />
	</fieldset>
</form>
