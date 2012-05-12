<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des adresses IP d'un membre</h1>

<p>Entrez le pseudo d'un membre pour afficher toutes les adresses IP connues qu'il a utilisées.</p>

<fieldset>
	<legend>Liste des adresses IP d'un membre</legend>
	<form method="get" action="">
		<label for="pseudo">Pseudo : </label> 
		<input type="text" name="pseudo" id="pseudo" /> 
		<input type="submit" value="Envoyer" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
		)) ?>
	</form>
</fieldset>
