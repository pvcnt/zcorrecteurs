<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un billet</h1>

<form action="" method="post">
	<div class="send">
		<input type="submit" name="submit" value="Envoyer" accesskey="s" tabindex="7" />
	</div>

	<fieldset>
		<legend>Modifier les informations</legend>
		<label for="titre">Titre (obligatoire) : </label>
		<input type="text" name="titre" id="titre" size="35" tabindex="1" value="<?php echo htmlspecialchars($InfosBillet['version_titre']); ?>" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" size="35" tabindex="2" value="<?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?>" /><br />

		<label for="categorie">Catégorie : </label>
		<select name="categorie" id="categorie" style="min-width: 150px;" tabindex="3">
			<?php
			foreach($Categories as $c){
				echo '<option value="'.$c['cat_id'].'"';
				if($InfosBillet['blog_id_categorie'] == $c['cat_id']) echo ' selected="selected"';
				echo '>'.htmlspecialchars($c['cat_nom']).'</option>';
			}
			?>
		</select>
	</fieldset>

	<div class="UI_rollbox">
		<div class="title">
			Lien hypertexte (ressource, site à visiter) :
			<?php if(!empty($InfosBillet['blog_lien_url'])){ ?>
			<a href="<?php echo htmlspecialchars($InfosBillet['blog_lien_url']); ?>">
				<?php echo !empty($InfosBillet['blog_lien_nom']) ?
				htmlspecialchars($InfosBillet['blog_lien_nom']) :
				htmlspecialchars($InfosBillet['blog_lien_url']); ?>
			</a>
			<?php } ?>
		</div>

		<div class="hidden">
			<label for="lien_nom">Titre : </label>
			<input type="text" name="lien_nom" id="lien_nom" size="35" value="<?php echo htmlspecialchars($InfosBillet['blog_lien_nom']); ?>" /><br />

			<label for="lien_url">URL : </label>
			<input type="text" name="lien_url" id="lien_url" size="35" value="<?php echo htmlspecialchars($InfosBillet['blog_lien_url']); ?>" /><br />
		</div>
	</div>

	<fieldset>
		<legend>Modifier le corps du billet</legend>
		<label for="intro">Introduction (obligatoire) : </label>
		<?php echo $view->render('::zform.html.php', array(
			'upload_id_formulaire' => $_GET['id'], 
			'upload_utiliser_element' => true, 
			'id' => 'intro', 
			'texte' => $InfosBillet['version_intro'],
			'tabindex' => 4,
		)) ?>
		<br /><br />

		<label for="texte">Contenu (obligatoire) : </label>
		<?php echo $view->render('::zform.html.php', array(
			'id' => 'texte', 
			'texte' => $InfosBillet['version_texte'], 
			'tabindex' => 5,
		)) ?>
	</fieldset>

	<fieldset>
		<legend>Résumé rapide des modifications</legend>
		<label for="commentaire">Note de version (facultatif) :</label>
		<input type="text" name="commentaire" id="commentaire" size="80" />
	</fieldset>

	<div class="send">
			<input type="submit" name="submit" value="Envoyer" accesskey="s" tabindex="6" />
		</div>
</form>
