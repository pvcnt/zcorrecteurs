<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un quiz</h1>

<form method="post" action="">
	<fieldset>
		<legend>Ajouter un quiz</legend>

		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" /><br />

		<label for="description">Description : </label>
		<input type="text" name="description" id="description" size="80" /><br />

		<label for="difficulte">Difficulté : </label>
		<select name="difficulte" id="difficulte">
			<?php foreach ($Difficultes as $cle => $valeur){ ?>
			<option value="<?php echo $cle; ?>">
				<?php echo htmlspecialchars($valeur); ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="categorie">Catégorie : </label>
		<select name="categorie" id="categorie">
			<?php foreach ($ListerCategories as $categorie){ ?>
			<option value="<?php echo $categorie['cat_id']; ?>">
				<?php echo htmlspecialchars($categorie['cat_nom']); ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="aleatoire">Nombre de réponses choisies dans un ordre aléatoire : </label>
		<select name="aleatoire" id="aleatoire">
                        <option value="0">0</option>
		<?php for($i = 2; $i <= 200; $i++) { ?>
			<option value="<?php echo $i ?>"><?php echo $i ?></option>
		<?php } ?>
		</select>
		<em>Le fait de choisir zéro permet d'afficher toutes les questions et dans l'ordre (mode aléatoire désactivé).</em>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
