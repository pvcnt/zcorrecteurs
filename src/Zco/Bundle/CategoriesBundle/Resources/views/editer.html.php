<?php use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

$view->extend('::layouts/default.html.php') ?>

<h1>Modifier une catégorie</h1>

<form method="post" action="" enctype="multipart/form-data">
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>

	<fieldset>
		<legend>Propriétés de la catégorie</legend>
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($InfosCategorie['cat_nom']); ?>" /><br />

		<label for="description">Description : </label>
		<input type="text" name="description" id="description" size="40" value="<?php echo htmlspecialchars($InfosCategorie['cat_description']); ?>" /><br />

		<label for="keywords">Mots-clés destinés aux robots : </label>
		<input type="text" name="keywords" id="keywords" size="40" value="<?php echo htmlspecialchars($InfosCategorie['cat_keywords']); ?>" /><br />

		<label for="url">URL : </label>
		<input type="text" name="url" id="url" size="40" value="<?php echo $InfosCategorie['cat_url']; ?>" />
		(<em>marqueurs : %id%, %id2%, %nom%</em>)<br />

		<label for="url_redir">URL de redirection : </label>
		<input type="text" name="url_redir" id="url_redir" size="40" value="<?php echo $InfosCategorie['cat_redirection']; ?>" />
		(<em>laisser vide pour ne pas créer une catégorie de redirection</em>)<br />

		<label for="parent">Catégorie parente : </label>
		<select name="parent" id="parent">
			<option value="0"<?php if($InfosCategorie['cat_niveau'] == 0) echo ' selected="selected"'; ?>>
				Aucune
			</option>
			<?php echo CategoryDAO::GetListeCategories($ListerParents[count($ListerParents)-1]['cat_id']); ?>
		</select><br/>
		<?php if ($isForum) : ?>
			<label for="archiver">Archiver le forum : </label>
			<input type="checkbox" name="archiver" id="archiver" <?php if($InfosCategorie['cat_archive'] == 1) echo 'checked'; ?> />
		<?php endif; ?>
	</fieldset>

	<fieldset>
		<legend>Paramètres publicitaires</legend>
		<label for="disponible_ciblage">Catégorie disponible au ciblage :</label>
		<input type="checkbox" name="disponible_ciblage" id="disponible_ciblage"<?php if ($InfosCategorie['cat_disponible_ciblage']) echo ' checked="checked"' ?> /><br />

		<label for="ciblage_actions">Les actions peuvent être ciblés une à une :</label>
		<input type="checkbox" name="ciblage_actions" id="ciblage_actions"<?php if ($InfosCategorie['cat_ciblage_actions']) echo ' checked="checked"' ?> />

	</fieldset>

	<fieldset>
		<legend style="cursor: pointer;" onclick="$('image').setStyle('display', ($('image').getStyle('display')=='none' ? 'block' : 'none')); return false;">
			Ajouter / modifier une image (cliquez pour afficher)
		</legend>

		<div id="image" style="display: none;">
		   <?php if($InfosCategorie['cat_image']) { ?>
		   <div style="float:right;">
		       <img src="/uploads/categories/<?php echo $InfosCategorie['cat_id'] ?>.png" alt="Image de la catégorie" /><br />
		       <label for="image_del"><input type="checkbox" name="image_del" id="image_del"> Supprimer l'image?</label>
		   </div>
		   <?php } ?>

		   <label for="image_file">Fichier : </label> <input type="file" name="image_file" id="image_file" /> ou<br />
		   <label for="image_url">URL : </label> <input type="text" name="image_url" id="image_url" /><br />
		   <em>L'image sera transformée en un .png d'une dimension de 80x80 pixels.</em>
		</div>
	</fieldset>

	<fieldset>
		<legend style="cursor: pointer;" onclick="$('forums').setStyle('display', ($('forums').getStyle('display')=='none' ? 'block' : 'none')); return false;">
			Paramètres avancés (cliquez pour afficher)
		</legend>

		<div id="forums" style="display: none;">
			<label for="texte">Règlement : </label>
			<?php echo $view->render('::zform.html.php', array('texte' => $InfosCategorie['cat_reglement'])); ?><br />

			<label for="map">MAP (contenu par défaut de la zForm sur le forum) : </label>
			<?php echo $view->render('::zform.html.php', array('id' => 'map', 'texte' => $InfosCategorie['cat_map'])) ?><br />

			<label for="type">Type de MAP : </label>
			<select name="type" id="type">
				<option value="<?php echo MAP_FIRST; ?>"<?php if($InfosCategorie['cat_map_type'] == MAP_FIRST) echo ' selected="selected"'; ?>>
					Appliquée sur le premier message d'un sujet
				</option>
				<option value="<?php echo MAP_ALL; ?>"<?php if($InfosCategorie['cat_map_type'] == MAP_ALL) echo ' selected="selected"'; ?>>
					Appliquée sur tous les messages
				</option>
			</select>
		</div>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
