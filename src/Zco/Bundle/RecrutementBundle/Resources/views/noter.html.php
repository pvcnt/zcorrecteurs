<?php $view->extend('::layouts/default.html.php') ?>

<h1>Noter la copie</h1>

<?php if(empty($InfosCandidature['candidature_correcteur_note'])){ ?>
<form method="post" action="">
	<fieldset>
		<legend>Attribuer une note à cette copie</legend>
		
		<label for="note">Pourcentage de fautes trouvées :</label>
		<input type="text" name="note" id="note" size="10" /> %

		<input type="submit" value="Envoyer" />
	</fieldset>
</form>
<?php } else{ ?>
Pourcentage de fautes trouvées : <?php echo $view['humanize']->numberformat($InfosCandidature['candidature_correcteur_note']) ?> %
<?php } ?>
