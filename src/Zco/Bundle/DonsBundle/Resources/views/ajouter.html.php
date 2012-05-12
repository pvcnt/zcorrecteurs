<?php $view->extend('::layouts/default.html.php') ?>

<h1>Enregistrer un don</h1>

<form method="post" action="">
	<fieldset>
		<legend>Enregistrer un don</legend>

		<label for="pseudo">Membre :</label>
		<input type="text" name="pseudo" id="pseudo" size="40" /><br />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
		)) ?>

		<label for="date">Date :</label>
		<?php echo $view->get('widget')->datePicker('date', date('Y-m-d')) ?><br />

		<label for="nom">Nom apparaissant en public :</label>
		<input type="text" name="nom" id="nom" size="40" />
		<em>Laisser vide pour afficher le pseudo du membre.</em>

		<div class="send"><input type="submit" name="submit" value="Envoyer" /></div>
	</fieldset>
</form>