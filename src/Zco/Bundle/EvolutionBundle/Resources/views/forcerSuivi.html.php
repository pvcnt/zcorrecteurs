<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Ajouter quelqu'un à la liste de suivi</h1>

<form method="post" action="">
	<fieldset>
		<legend>Choix du membre</legend>
		<label for="pseudo" class="nofloat">Pseudo :</label>
		<input type="text" name="pseudo" id="pseudo" />
		<input type="submit" value="Envoyer" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->path('zco_user_api_searchUsername'),
		)) ?>
	</fieldset>
</form>
