<?php $view->extend('::layouts/default.html.php') ?>

<h1>Indiquer une période d'absence</h1>

<p>
	Vous pouvez indiquer que vous êtes absent. Cette option fera apparaitre dans tous vos messages
	à côté de votre pseudo une icône <img src="/bundles/zcooptions/img/gerer_absence.png" alt="Absent" />
	et la raison de votre absence apparaitra sur votre profil.
</p>

<form method="post" action="">
	<fieldset>
		<legend>Gérer ses absences</legend>
		<?php if(!empty($InfosMembre['utilisateur_debut_absence'])){ ?>
		Une absence est définie actuellement pour le <?php echo date("d/m/Y", strtotime($InfosMembre['utilisateur_debut_absence'])); ?>. Celle-ci prend fin
		<?php if(is_null($InfosMembre['utilisateur_fin_absence']))
					{
						echo 'dans une durée indéterminée.';
					}
					else
					{
						echo dateformat($InfosMembre['utilisateur_fin_absence'], DATE, MINUSCULE).'.';
					} ?><br />
		<label for="delete_abs"> Lever cette absence : </label>
		<input type="checkbox" name="delete_abs" id="delete_abs" tabindex="1" />
		<?php } else { ?>
		<p><label for="debut_abs">Début de l'absence : </label>
		<?php //echo $view->get('widget')->datePicker('debut_abs', date("d/m/Y"), array('format' => 'd/m/Y', 'inputOutputFormat' => 'd/m/Y')); ?><input type="text" name="debut_abs" id="debut_abs" maxlength="10" value="<?php echo date("d/m/Y"); ?>" /> <em>Format : jj/mm/aaaa (Si vide, l'absence prendra effet dès validation du formulaire)</em></p>
		<p><label for="temps_abs">Durée de l'absence : </label>
		<input type="text" size="2" maxlength="2" tabindex="1" name="temps_abs" id="temps_abs" />
		<select name="duree_abs">
			<option value="0">Indéterminée</option>
			<option value="1">… jours</option>
			<option value="2">… mois</option>
			<option value="3">… ans</option>
		</select></p> ou
		<p><label for="fin_abs">Fin de l'absence : </label>
		<?php //echo $view->get('widget')->datePicker('fin_abs', '', array('format' => 'd/m/Y', 'inputOutputFormat' => 'd/m/Y')); ?><input type="text" name="fin_abs" id="fin_abs" maxlength="10" /> <em>Format : jj/mm/aaaa</em></p>
	</fieldset>

	<fieldset>
		<legend>Motif de l'absence</legend>
		<?php echo $view->render('::zform.html.php'); ?>
	<?php } ?>
	</fieldset>

	<div class="send">
		<input type="submit" name="submit" value="Envoyer" />
	</div>
</form>

<?php if($InfosMembre['utilisateur_absent']==1 && !empty($InfosMembre['utilisateur_motif_absence']))
			{
				echo $view['messages']->parse('<citation nom="Motif de l\'absence">'.$InfosMembre['utilisateur_motif_absence'].'</citation>');
			} ?>
