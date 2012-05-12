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

		<label for="aleatoire">Réponses choisies dans un ordre aléatoire : </label>
		<input type="checkbox" name="aleatoire" id="aleatoire" />
		<em>Si vous cochez cette case, <?php echo QUIZ_ALEATOIRE_NB_QUESTIONS; ?> réponses seront choisies aléatoirement à chaque fois qu'un utilisateur y jouera.</em>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
