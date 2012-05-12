<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier mon adresse mail</h1>

<?php if($_GET['id'] == $_SESSION['id']){ ?>
<p>
	Vous recevrez un mail à l'adresse indiquée. Vous devrez alors valider votre nouvelle adresse
	en cliquant sur le lien contenu dans ce mail.
</p>
<?php } ?>

<form action="" method="post">
	<fieldset>
		<legend>Modifier mon adresse mail</legend>
			<?php if($_GET['id'] == $_SESSION['id']){ ?>
			<label for="mot_passe">Mot de passe actuel : </label>
			<input type="password" name="mot_passe" id="mot_passe" /><br />
			<?php } ?>

			<label for="mail">Nouvelle adresse mail : </label>
			<input type="text" name="mail" id="mail" /><br />

			<input type="submit" name="submit" value="Envoyer" />
	</fieldset>
</form>
