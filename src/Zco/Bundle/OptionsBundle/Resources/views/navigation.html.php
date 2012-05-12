<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier les options de navigation <?php if(empty($_GET['id'])) echo 'par défaut'; ?></h1>

<?php if(empty($_GET['id'])){ ?>
<p>
	Les options de navigation par défaut correspondent aux options utilisées par les visiteurs, et par
	les membres nouvellement inscrits. Ceux-ci pourront ensuite les modifier via leur page d'options.
</p>
<?php } ?>

<form action="" method="post">
	<fieldset>
		<legend>Interface</legend>
		<label for="temps_redirection">Temps de redirection : </label>
		<input type="text" name="temps_redirection" id="temps_redirection" size="2" value="<?php echo $InfosMembre['preference_temps_redirection']; ?>" />
		<em>En secondes ; 0 pour ne pas afficher la page de redirection.</em><br />

		<?php if (verifier('admin')): ?>
		<label for="afficher_admin_rapide">Afficher le menu d'administration rapide : </label>
		<input type="checkbox" name="afficher_admin_rapide" id="afficher_admin_rapide" <?php if($InfosMembre['preference_afficher_admin_rapide']) echo 'checked="checked" '; ?>/>
		<br /><br />
		<?php endif ?>
		
		<label for="activer_rep_rapide">Activer la réponse rapide : </label>
		<input type="checkbox" name="activer_rep_rapide" id="activer_rep_rapide" <?php if($InfosMembre['preference_activer_rep_rapide']) echo 'checked="checked" '; ?>/><br />

		<label for="afficher_signatures">Afficher les signatures sur le forum : </label>
		<input type="checkbox" name="afficher_signatures" id="afficher_signatures" <?php if($InfosMembre['preference_afficher_signatures']) echo 'checked="checked" '; ?>/>
	</fieldset>

	<fieldset>
		<legend>Notifications</legend>
		<label for="activer_email_mp">M'avertir par email quand je reçois un MP : </label>
		<input type="checkbox" name="activer_email_mp" id="activer_email_mp" <?php if($InfosMembre['preference_activer_email_mp']) echo 'checked="checked" '; ?>/><br />
	</fieldset>
    
	<div class="send">
		<input type="submit" name="submit" value="Envoyer" />
	</div>
</form>
