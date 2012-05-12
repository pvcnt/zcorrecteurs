<?php $view->extend('::layouts/default.html.php') ?>

<h1>Suppression de sujets</h1>

<form method="post" action="">
	<input type="hidden" name="action" value="supprimer" />
	<?php foreach($_POST['sujet'] as $sujet => $valeur){ ?>
	<input type="hidden" name="sujet[]" value="<?php echo $sujet; ?>" />
	<?php } ?>

	<fieldset>
		Êtes-vous sûr de vouloir supprimer tous les sujets sélectionnés ainsi que les messages qu'ils contiennent ?<br />
		<strong>La suppression de plusieurs sujets est gourmande en requêtes. Préférez la mise en corbeille.</strong><br /><br />

		<div class="centre">
			<input type="submit" name="confirmer" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</fieldset>
</form>