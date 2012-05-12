<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosQuestion->Quiz['nom']) ?></h1>

<form method="post" action="">
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>

	<fieldset>
		<legend>Question</legend>
		<label for="question">Question : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'question', 'texte' => $InfosQuestion['question'])) ?>
	</fieldset>

	<fieldset>
		<legend>Réponses</legend>
		<label for="rep1">Réponse 1 : </label>
		<input type="text" name="rep1" id="rep1" value="<?php echo htmlspecialchars($InfosQuestion['reponse1']); ?>" size="50" />
		<em>Champ obligatoire.</em><br />

		<label for="rep2">Réponse 2 : </label>
		<input type="text" name="rep2" id="rep2" value="<?php echo htmlspecialchars($InfosQuestion['reponse2']); ?>" size="50" />
		<em>Champ obligatoire.</em><br />

		<label for="rep3">Réponse 3 : </label>
		<input type="text" name="rep3" id="rep3" value="<?php echo htmlspecialchars($InfosQuestion['reponse3']); ?>" size="50" /><br />

		<label for="rep4">Réponse 4 : </label>
		<input type="text" name="rep4" id="rep4" value="<?php echo htmlspecialchars($InfosQuestion['reponse4']); ?>" size="50" /><br />

		<label for="rep_juste">Réponse juste : </label>
		<select name="rep_juste" id="rep_juste">
			<?php for ($i = 1 ; $i <= 4 ; $i++){ ?>
			<option value="<?php echo $i ?>"<?php if ($InfosQuestion['reponse_juste'] == $i) echo ' selected="selected"'; ?>>
				Réponse <?php echo $i ?>
			</option>
			<?php } ?>
		</select>
	</fieldset>

	<fieldset>
		<legend>Explication</legend>
		<label for="texte">Explication : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'texte', 'texte' => $InfosQuestion['explication'])) ?>
	</fieldset>

	<br />
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
