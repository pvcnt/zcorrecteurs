<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Modifier l'anomalie</h1>

<div class="UI_column_menu">
	<?php include(dirname(__FILE__).'/_actions_anomalie.html.php'); ?>
</div>

<div class="UI_column_text">
	<form method="post" action="">
		<fieldset>
			<legend>Description rapide de la demande</legend>
			<label for="titre">Titre :</label>
			<input type="text" name="titre" tabindex="1" id="titre" size="40" maxlength="255" value="<?php echo htmlspecialchars($InfosTicket['ticket_titre']); ?>" /><br />

			<?php if(verifier('tracker_voir_prives')){ ?>
			<label for="prive">Cette demande concerne une partie privée du site :</label>
			<input type="checkbox" name="prive" tabindex="3" id="prive"<?php if($InfosTicket['ticket_prive']) echo ' checked="checked"'; ?> /><br /><br />
			<?php } ?>
		</fieldset>

		<fieldset>
			<legend>Description détaillée</legend>
			<label for="lien">Lien (facultatif) :</label>
			<input type="text" name="lien" id="lien" tabindex="4" size="40" value="<?php echo htmlspecialchars($InfosTicket['ticket_url']); ?>" /><br />

			<label for="texte">Causes de l'anomalie, comment la reproduire, etc. :</label><br /><br />
			<?php echo $view->render('::zform.html.php', array('tabindex' => 5, 'texte' => $InfosTicket['ticket_description'])); ?>
		</fieldset>

		<div class="centre">
			<input type="submit" name="send" value="Envoyer" tabindex="6" />
		</div>
	</form>
</div>
