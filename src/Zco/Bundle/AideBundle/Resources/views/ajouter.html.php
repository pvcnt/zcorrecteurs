<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un nouveau sujet d'aide</h1>

<form method="post" action="">
	<div class="send"><input type="submit" value="Envoyer" /></div>

	<fieldset>
		<legend>Propriétés du sujet d'aide</legend>

		<label for="titre">Titre du sujet :</label>
		<input type="text" name="titre" id="titre" size="40" tabindex="1" /><br />

		<label for="categorie">Catégorie :</label>
		<select name="categorie" id="categorie" tabindex="2">
			<?php foreach ($categories as $cat){ ?>
			<option value="<?php echo $cat['id'] ?>">
				<?php echo htmlspecialchars($cat['nom']) ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="racine">Afficher sur l'accueil de l'aide :</label>
		<input type="checkbox" name="racine" id="racine" checked="checked" tabindex="3" /><br />

		<label for="icone">Lien vers une icone (facultatif) :</label>
		<input type="text" name="icone" id="icone" size="40" tabindex="4" /><br />
	</fieldset>

	<fieldset>
		<legend>Contenu de l'aide</legend>
		<?php echo $view->render('::zform.html.php', array('tabindex' => 5)) ?>
	</fieldset>

	<div class="send"><input type="submit" value="Envoyer" tabindex="6" /></div>
</form>