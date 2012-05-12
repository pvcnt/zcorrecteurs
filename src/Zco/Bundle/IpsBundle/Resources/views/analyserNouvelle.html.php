<?php $view->extend('::layouts/default.html.php') ?>

<h1>Analyser une adresse IP</h1>

<p>Entrez une adresse IP à analyser. Vous verrez alors la liste des actions effectuées par un membre répondant à cette IP, ainsi que les membres l'ayant utilisée.</p>

<fieldset>
	<legend>Analyser une adresse IP</legend>
	<form method="get" action="">
		<label for="ip">Adresse IP : </label> <input type="text" name="ip" id="ip" /> <input type="submit" value="Envoyer" />
	</form>
</fieldset>
