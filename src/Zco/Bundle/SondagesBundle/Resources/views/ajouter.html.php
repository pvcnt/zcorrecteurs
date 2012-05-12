<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un sondage</h1>

<form method="post" action="">
	<fieldset>
		<legend>Informations sur le sondage</legend>

		<label for="nom">Nom du songage : </label>
		<input type="text" tabindex="1" name="nom" id="nom" size="40" /><br />

		<label for="texte">Description : </label>
		<?php echo $view->render('::zform.html.php', array('tabindex' => 2)) ?><br />

		<label for="date_debut">Date de début : </label>
		<?php echo $view->get('widget')->dateTimePicker('date_debut', date('Y-m-d H:i:s')) ?><br />

		<label for="date_fin">Date de fin : </label>
		<?php echo $view->get('widget')->dateTimePicker('date_fin', null, array('allowEmpty' => true)) ?>
		<em>Laisser vide pour jamais.</em><br />

		<label for="ouvert">Sondage visible : </label>
		<input type="checkbox" name="ouvert" id="ouvert" />

		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>