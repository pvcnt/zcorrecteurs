<?php $view->extend('::layouts/default.html.php') ?>

<h1>Envoyer un message</h1>
<fieldset>
<legend>Ajout d'un message privé</legend>
<form action="" method="post">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<label for="titre">Titre : </label>
		<input type="text" name="titre" id="titre" value="<?php if(!empty($_POST['titre'])) echo htmlspecialchars($_POST['titre']); ?>" size="35" tabindex="1" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" value="<?php if(!empty($_POST['sous_titre'])) echo htmlspecialchars($_POST['sous_titre']); ?>" size="35" tabindex="2" />
		<br /><br />
		<?php if(isset($Pseudo) && $Pseudo['utilisateur_absent']==1) { ?>
            <p class="UI_infobox">
                Le membre <strong><?php echo htmlspecialchars($Pseudo['utilisateur_pseudo']) ?></strong>
                auquel vous vous apprêtez à envoyer un MP est marqué comme absent. Il se peut donc qu'il soit long à répondre.
            </p>
        <?php } ?>
		<label for="pseudo">Destinataire : </label>
		<input name="pseudo" id="pseudo" tabindex="3" size="35" value="<?php if(!empty($_POST['pseudo'])) echo htmlspecialchars($_POST['pseudo']); ?>" /> <input type="button" name="ajouter_destinataire" value="Ajouter à la liste" onclick="this.form.destinataires.value += this.form.pseudo.value+'\n';this.form.pseudo.value = '';this.form.pseudo.focus();" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->path('zco_user_api_searchUsername'),
		)) ?>
		
		<br />
		<label for="destinataires">Destinataires multiples :
		<br /><strong>(<?php echo PM_MAX_PARTICIPANTS - 1 ?> participants max.)</strong>
		</label>
		<textarea name="destinataires" id="destinataires" tabindex="4" class="mp_destinataires"><?php if(!empty($_POST['destinataires'])) echo htmlspecialchars($_POST['destinataires']); ?></textarea>
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/tableaux_messages.css') /* Style du textarea ci-dessus */ ?>
		<br />

		<?php echo $view->render('::zform.html.php'); ?>

		<div class="cleaner">&nbsp;</div>

		<label for="dossier">Dossier de destination : </label>
		<select name="dossier" id="dossier">
		<option value="0">Accueil</option>
		<?php
		foreach($ListerDossiers as $valeur)
		{
			echo '<option value="'.$valeur['mp_dossier_id'].'">'.$valeur['mp_dossier_titre'].'</option>';
		}
		?>
		</select>

		<?php
		if(verifier('mp_fermer'))
		{
			echo '<br /><label for="ferme">MP fermé : </label><input type="checkbox" name="ferme" id="ferme" value="ferme" />';
		}
		?>

		<div class="cleaner">&nbsp;</div>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

