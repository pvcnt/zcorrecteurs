<?php $view->extend('::layouts/light.html.php') ?>

<?php if ($Auteur !== null): ?>
	<script type="text/javascript" language="javascript">
		var opt = new Element('option', {
			'value': '<?php echo $Auteur->id ?>',
			'text':  '<?php echo str_replace("'", "\\'", $Auteur) ?>'
		});

		var lst = window.opener.document.getElementById(location.hash.substring(1));
		var nbChoices = lst.getElementsByTagName('option').length;

		opt.inject(lst);
		lst.selectedIndex = nbChoices;

		self.close();
	</script>

	<p>Vous pouvez maintenant fermer cette fenêtre.</p>
<?php else: ?>
	<h1>Ajouter un auteur</h1>

	<form action="" method="post">
		<fieldset>
			<legend>Auteur</legend>

			<label for="prenom">Prénom :</label>
			<input type="text" name="prenom" id="prenom" maxsize="100" /><br />

			<label for="nom">Nom :</label>
			<input type="text" name="nom" id="nom" maxsize="100" />
			<span style="color: red">*</span><br />

			<label for="autres">Autres :</label>
			<input type="text" name="autres" id="autres" maxsize="100"/><br />
		</fieldset>

		<p class="centre"><input type="submit" value="Enregistrer"/></p>

		<fieldset>
			<legend>Description (pourra être complété plus tard)</legend>
			<?php echo $view->render('::zform.html.php', array('id' => 'description', 'texte' => '')) ?>
		</fieldset>

		<p class="centre"><input type="submit" value="Enregistrer"/></p>
	</form>
<?php endif ?>
