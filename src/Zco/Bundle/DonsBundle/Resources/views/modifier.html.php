<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un don</h1>

<form method="post" action="">
	<fieldset>
		<legend>Modifier un don</legend>

		<label for="date">Date :</label>
		<?php echo $view->get('widget')->datePicker('date', $don['date']) ?><br />

		<label for="nom">Nom apparaissant en public :</label>
		<input type="text" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($don['nom']) ?>" />
		<em>Laisser vide pour afficher le pseudo du membre.</em>
		
		<div class="send"><input type="submit" name="submit" value="Envoyer" /></div>
	</fieldset>
</form>