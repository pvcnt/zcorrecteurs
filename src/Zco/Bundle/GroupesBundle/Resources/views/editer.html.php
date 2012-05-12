<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un groupe</h1>

<fieldset>
	<legend>Modifier un groupe</legend>
	<form method="post" action="">
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom"  size="40" value="<?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?>" /><br />

		<label for="description">Description : </label>
		<input type="text" name="description" id="description" size="40" value="<?php echo htmlspecialchars($InfosGroupe['groupe_description']); ?>" /><br />

		<label for="logo">Adresse du logo masculin: </label>
		<input type="text" name="logo" id="logo" size="40" value="<?php echo htmlspecialchars($InfosGroupe['groupe_logo']); ?>" /><br />

		<label for="logo_feminin">Adresse du logo féminin : </label>
		<input type="text" name="logo_feminin" id="logo_feminin" size="40" value="<?php echo htmlspecialchars($InfosGroupe['groupe_logo_feminin']); ?>" /><br />

		<label for="class">Couleur : </label>
		<input type="text" name="class" id="class" size="40" value="<?php echo htmlspecialchars($InfosGroupe['groupe_class']); ?>" /><br />

		<label for="sanction">Ce groupe est une sanction : </label>
		<input type="checkbox" name="sanction" id="sanction" <?php if($InfosGroupe['groupe_sanction']) echo 'checked="checked" '; ?>/><br />

		<label for="team">Ce groupe appartient à l'équipe : </label>
		<input type="checkbox" name="team" id="team" <?php if($InfosGroupe['groupe_team']) echo 'checked="checked" '; ?>/><br />
		
		<label for="team">Ce groupe est secondaire : </label>
		<input type="checkbox" name="secondaire" id="secondaire" <?php if($InfosGroupe['groupe_secondaire']) echo 'checked="checked" '; ?> /><br />

		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</form>
</fieldset>
