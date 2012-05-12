<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier une candidature</h1>

<form method="post" action="">
	<fieldset>
		<legend>Modifier une candidature</legend>
		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>

		<label for="statut">Statut : </label>
		<select name="statut" id="statut">
			<?php $ListeEtatCandidature  = array(CANDIDATURE_REDACTION => 'Rédaction', CANDIDATURE_ENVOYE => 'Envoyée', CANDIDATURE_ATTENTE_TEST => 'En test', CANDIDATURE_TESTE => 'Testée', CANDIDATURE_ACCEPTE => 'Acceptée', CANDIDATURE_REFUSE => 'Refusée', CANDIDATURE_DESISTE => 'Désistement');
			foreach($ListeEtatCandidature as $numEtat => $nomEtat) {
			?><option value="<?php echo $numEtat; ?>" <?php echo ($InfosCandidature['candidature_etat']==$numEtat) ? 'selected="selected" ' : ''; ?>><?php echo $nomEtat; ?></option>
			<?php } ?>
		</select><br />

		<?php if($InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST){ ?>
		<label for="date_fin">Fin de correction du test : </label>
		<?php echo $view->get('widget')->dateTimePicker('date_fin', $InfosCandidature['candidature_date_fin_correction']); ?><br /><br />

		<?php if($InfosCandidature['candidature_test_type'] == TEST_TEXTE){ ?>
		<label for="texte">Texte à corriger : </label>
		<?php echo $view->render('::zform.html.php', array('texte' => $InfosCandidature['candidature_correction_original'])) ?><br />
		<?php } } ?>

		<label for="motiv">Texte de motivation : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'motiv', 'texte' => $InfosCandidature['candidature_texte'])) ?>
		<br />
		<?php if($InfosCandidature['recrutement_redaction']) { ?>
		<br />
		<label for="redaction">Rédaction : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'redaction', 'texte' => $InfosCandidature['candidature_redaction'])) ?>
		<?php } ?>

		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>
