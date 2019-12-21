<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter une catégorie</h1>

<form method="post" action="" enctype="multipart/form-data">
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>

	<fieldset>
		<legend>Propriétés de la catégorie</legend>
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" /><br />

		<label for="description">Description : </label>
		<input type="text" name="description" id="description" size="40" /><br />

		<label for="keywords">Mots-clés destinés aux robots : </label>
		<input type="text" name="keywords" id="keywords" size="40" /><br />

		<label for="url">URL : </label>
		<input type="text" name="url" id="url" size="40" /> (
		<em>marqueurs : %id%, %id2%, %nom%</em>)<br />

		<label for="url_redir">URL de redirection : </label>
		<input type="text" name="url_redir" id="url_redir" size="40" />
		(<em>laisser vide pour ne pas créer une catégorie de redirection</em>)<br />

		<label for="parent">Catégorie parente : </label>
		<select name="parent" id="parent">
			<?php if(empty($categories)) { ?><option value="0">Aucune</option><?php } ?>
			<?php echo GetListeCategories(); ?>
		</select>
	</fieldset>

	<fieldset>
		<legend>Paramètres publicitaires</legend>
		<label for="disponible_ciblage">Catégorie disponible au ciblage :</label>
		<input type="checkbox" name="disponible_ciblage" id="disponible_ciblage" checked="checked" /><br />

		<label for="ciblage_actions">Les actions peuvent être ciblés une à une :</label>
		<input type="checkbox" name="ciblage_actions" id="ciblage_actions" checked="checked" />

	</fieldset>

	<fieldset>
		<legend>Paramétrage des droits</legend>
		<label for="cat">Copier les droits d'une catégorie : </label>
		<select name="cat" id="cat">
			<option value="0" selected="selected">N'attribuer aucun droit</option>
			<?php echo GetListeCategories(); ?>
		</select>
	</fieldset>

	<fieldset>
		<legend style="cursor: pointer;"onclick="if(document.getElementById('forums').style.display=='none') document.getElementById('forums').style.display='block'; else document.getElementById('forums').style.display='none'; return false;">Paramètrage des forums (cliquez pour afficher)</legend>
		<div id="forums" style="display: none;">
			<p>Ces paramètres ne sont utiles qu'au catégories définissant des forums. Ils ne s'appliquent que sur ce forum, jamais sur ses enfants.</p>
			<label for="texte">Règlement : </label>
			<?php echo $view->render('::zform.html.php'); ?><br />

			<label for="map">MAP : </label>
			<?php echo $view->render('::zform.html.php', array('id' => 'map')); ?><br />

			<label for="type">Type de map : </label>
			<select name="type" id="type">
				<option value="<?php echo MAP_FIRST; ?>">
					Appliquée sur le premier message d'un sujet
				</option>
				<option value="<?php echo MAP_ALL; ?>">
					Appliquée sur tous les messages
				</option>
			</select>
		</div>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
