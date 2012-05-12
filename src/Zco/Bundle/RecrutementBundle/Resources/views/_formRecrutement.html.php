<form action="" method="post" <?php echo $view['form']->enctype($form) ?>>
    <?php echo $view['form']->errors($form) ?>
	<fieldset>
		<legend>Présentation du recrutement</legend>
	    <?php echo $view['form']->row($form['nom']) ?>
		<?php echo $view['form']->row($form['Groupe']) ?>
		<?php echo $view['form']->row($form['etat']) ?>
		<?php echo $view['form']->row($form['date']) ?>
		<?php echo $view['form']->row($form['date_fin_depot']) ?>
		<?php echo $view['form']->row($form['texte']) ?>
		<?php echo $view['form']->row($form['lien']) ?>
	</fieldset>
	
	<fieldset>
		<legend>Configuration des épreuves</legend>
		<?php echo $view['form']->row($form['redaction'], array(
			'help' => 'En cochant cette case, les candidats devront envoyer une rédaction en même temps que leur candidature.',
		)) ?>
		<?php echo $view['form']->row($form['test'], array(
			'help' => 'En cochant cette case, les candidats devront passer une épreuve de correction si leur candidature est retenue.',
		)) ?>
		<?php echo $view['form']->row($form['Quiz'], array(
			'help' => 'Si vous sélectionnez un quiz, les candidats devront y répondre lors de l\'envoi de leur candidature.',
		)) ?>
	</fieldset>

	<?php echo $view['form']->rest($form) ?>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>