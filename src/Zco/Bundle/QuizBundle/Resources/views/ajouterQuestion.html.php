<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Ajouter une question au quiz</h1>

<form method="post" action="">
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>

	<fieldset>
		<legend>Question</legend>
		<label for="question">Question : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'question')) ?>
	</fieldset>

	<fieldset>
		<legend>Réponses</legend>
		<label for="rep1">Réponse 1 : </label>
		<input type="text" name="rep1" id="rep1" size="50" />
		<em>Champ obligatoire.</em><br />

		<label for="rep2">Réponse 2 : </label>
		<input type="text" name="rep2" id="rep2" size="50" />
		<em>Champ obligatoire.</em><br />

		<label for="rep3">Réponse 3 : </label>
		<input type="text" name="rep3" id="rep3" size="50" /><br />

		<label for="rep4">Réponse 4 : </label>
		<input type="text" name="rep4" id="rep4" size="50" /><br />

		<label for="rep_juste">Réponse juste : </label>
		<select name="rep_juste" id="rep_juste">
			<option value="1">Réponse 1</option>
			<option value="2">Réponse 2</option>
			<option value="3">Réponse 3</option>
			<option value="4">Réponse 4</option>
		</select>
	</fieldset>

	<fieldset>
		<legend>Explication</legend>
		<label for="texte">Explication : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'texte')); ?>
	</fieldset>

	<br />
	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>
