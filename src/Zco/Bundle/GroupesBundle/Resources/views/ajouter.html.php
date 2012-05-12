<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un groupe</h1>

<fieldset>
	<legend>Ajouter un groupe</legend>
	<form method="post" action="">
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" /><br />

		<label for="logo">Adresse du logo : </label>
		<input type="text" name="logo" id="logo" size="40" /><br />

		<label for="logo_feminin">Adresse du logo féminin : </label>
		<input type="text" name="logo_feminin" id="logo_feminin" size="40" /><br />

		<label for="class">Couleur : </label>
		<input type="text" name="class" id="class" size="40" /><br />

		<label for="sanction">Ce groupe est une sanction : </label>
		<input type="checkbox" name="sanction" id="sanction" /><br />

		<label for="team">Ce groupe appartient à la team : </label>
		<input type="checkbox" name="team" id="team" /><br />

		<label for="team">Ce groupe est secondaire : </label>
		<input type="checkbox" name="secondaire" id="secondaire" /><br />

		<label for="groupe">Copier les droits d'un groupe : </label>
		<select id="groupe" name="groupe">
			<option value="0" selected="selected">Aucun</option>
			<?php foreach ($ListerGroupes as $g){ ?>
			<?php if ($g['groupe_id'] != GROUPE_VISITEURS){ ?>
				<option value="<?php echo $g['groupe_id']; ?>" style="color: <?php echo $g['groupe_class']; ?>;">
					<?php echo htmlspecialchars($g['groupe_nom']); ?>
				</option>
			<?php } } ?>
		</select>

		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</form>
</fieldset>
