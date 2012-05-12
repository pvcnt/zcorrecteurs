<?php $view->extend('::layouts/default.html.php') ?>

<h1>Recharger les droits</h1>

<form method="post" action="">
	<fieldset>
		<legend>Recharger les droits</legend>
		<p>Lorsqu'un membre se connecte, son groupe d'appartenance est stocké chez lui jusqu'à ce qu'il se déconnecte ou que la session
		soit fermée (et de même pour ses droits, les pages qu'il peut visiter, etc.). Vous pouvez choisir de mettre à jour automatiquement
		les appartenances en validant cette page. Ceci n'est à faire qu'en cas de problème, ou de changement des droits, car cela alourdit
		le chargement de la page.</p>

		<p>Voulez-vous vraiment recharger le cache des groupes ?</p>

		<p class="centre"><input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" /></p>
	</fieldset>
</form>