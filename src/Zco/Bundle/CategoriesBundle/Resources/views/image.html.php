<?php $view->extend('::layouts/default.html.php') ?>

<h1>Graphique des catégories</h1>

<form method="post" action="graphique.html">
	<fieldset>
		<legend>Catégorie à visualiser</legend>
		<label for="id">Sélectionnez une catégorie : </label>
		<select name="id" id="id">
			<?php if(empty($categories)) { ?><option value="0">Aucune</option><?php } ?>
			<?php echo GetListeCategories(); ?>
		</select><br/>

		<label for="id2">Afficher les bornes :</label>
		<input type="checkbox" name="id2" id="id2"/><br/>

		<label>Orientation :</label>
		<input type="radio" name="orientation" value="0" checked="checked" /> Horizontal
		<input type="radio" name="orientation" value="1" /> Vertical
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
