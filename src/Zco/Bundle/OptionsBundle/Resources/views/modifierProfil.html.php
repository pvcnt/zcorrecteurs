<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier le profil</h1>

<p>
	Vous pouvez choisir les informations que vous souhaitez voir apparaitre sur
	<a href="/membres/profil-<?php echo $_GET['id']; ?>-<?php echo rewrite($_SESSION['pseudo']); ?>.html">la page de profil</a>. Notez bien que rien
	n'est obligatoire, vous pouvez choisir de garder certaines informations confidentielles.
</p>

<form action="" method="post" enctype="multipart/form-data" id="form_profil">
	<div class="send">
		<input type="submit" name="submit" value="Modifier"/>
	</div>
	<?php if($_GET['id'] == $_SESSION['id']){ ?>
		<p>
			Note : pour changer votre pseudo sur ce site, vous devez
			<a href="<?php echo $view['router']->generate('zco_user_newPseudo') ?>">procéder à une demande de changement de pseudo</a>.
		</p>
	<?php } ?>

	<fieldset>
		<legend id="im">Moyens de communication</legend>
		<label for="afficher_mail">Afficher mon adresse courriel : </label>
		<input type="checkbox" name="afficher_mail" id="afficher_mail"<?php if($InfosMembre['utilisateur_afficher_mail']) echo ' checked="checked"'; ?> /><br />

		<label for="msn">MSN : </label>
		<input type="text" name="msn" id="msn" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_msn']); ?>" /><br />

		<label for="aim">AIM : </label>
		<input type="text" name="aim" id="aim" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_aim']); ?>" /><br />

		<label for="icq">ICQ : </label>
		<input type="text" name="icq" id="icq" value="<?php echo $InfosMembre['utilisateur_icq']; ?>" /><br />

		<label for="jabber">Jabber : </label>
		<input type="text" name="jabber" id="jabber" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_jabber']); ?>" /><br />

		<label for="skype">Skype : </label>
		<input type="text" name="skype" id="skype" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_skype']); ?>" />

		<?php if(verifier('options_ajouter_cle_pgp')){ ?><br />
		<label for="cle_pgp">Clé PGP :</label>
		<textarea name="cle_pgp" id="cle_pgp" style="width: 35%; height: 100px;"><?php echo htmlspecialchars($InfosMembre['utilisateur_cle_pgp']); ?></textarea><br />
		<strong>Ne renseignez que votre clé publique bien entendu !</strong>
		<?php } ?>
	</fieldset>

	<fieldset>
		<legend id="geolocalisation">Localisation</legend>
		<?php if(verifier('modifier_adresse')){ ?>
		<label for="utilisateur_adresse">Adresse (ou ville) :</label>
		<input type="text" name="utilisateur_adresse" id="utilisateur_adresse" value="<?php echo $InfosMembre['utilisateur_adresse'];?>" onFocus="this.form.coordonnees.value = '';" />
		<input type="button" onclick="chercher_adresse(this.form.utilisateur_adresse.value, <?php echo $InfosMembre['utilisateur_id']; ?>);" value="Trouver les coordonnées" /><br />

		<label for="coordonnees">Coordonnées :</label>
		<input type="text" name="coordonnees" id="coordonnees" value="<?php echo $InfosMembre['utilisateur_latitude'].', '.$InfosMembre['utilisateur_longitude']; ?>" readonly="readonly" /><br />
		<?php } ?>

        <label for="afficher_pays">Afficher mon pays : </label>
        <input type="checkbox" name="afficher_pays" id="afficher_pays"<?php if($InfosMembre['utilisateur_afficher_pays']) echo ' checked="checked"'; ?> />
        <em>Votre pays peut être automatiquement détecté et affiché sur votre profil.</em><br />
    </fieldset>

	<fieldset>
		<legend id="infos">Informations personnelles</legend>
		<label for="profession">Profession/études : </label>
		<input type="text" name="profession" id="profession" size="40" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_profession']); ?>" /><br />

		<label for="passions">Passions : </label>
		<input type="text" name="passions" id="passions" size="40" maxlength="60" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_passions']); ?>" /><br />

		<label for="date_naissance">Date de naissance : </label>
		<input type="text" name="date_naissance" id="date_naissance" value="<?php echo $date_naissance; ?>" />
		(<em>jj/mm/aaaa</em>)<br />

		<?php if(verifier('modifier_sexe'))
		{
		?>
		<label for="sexe">Sexe : </label>
		<select name="sexe">
			<option value="0">Non spécifié</option>
			<option value="<?php echo SEXE_MASCULIN; ?>"<?php echo (($InfosMembre['utilisateur_sexe'] == SEXE_MASCULIN) ? ' selected' : ''); ?>>Masculin</option>
			<option value="<?php echo SEXE_FEMININ; ?>"<?php echo (($InfosMembre['utilisateur_sexe'] == SEXE_FEMININ) ? ' selected' : ''); ?>>Féminin</option>
		</select><br />
		<?php
		}
		?>
		<label for="site_web">Site web : </label>
		<input type="text" name="site_web" id="site_web" size="40" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_site_web']); ?>" />
	</fieldset>

	<fieldset>
		<legend id="personnalite">Personnalité</legend>
		<label for="quote">Citation : </label>
		<input type="text" name="citation" id="quote" maxlength="30" value="<?php echo htmlspecialchars($InfosMembre['utilisateur_citation']); ?>" />
		<em>Votre citation sera affichée au-dessus de votre avatar.</em><br /><br />

		<label for="signature">Signature :</label>
		<?php echo $view->render('::zform.html.php', array('id' => 'signature', 'texte' => $InfosMembre['utilisateur_signature'])) ?>
		<br /><br />

		<label for="biographie">Biographie :</label>
		<?php echo $view->render('::zform.html.php', array('id' => 'biographie', 'texte' => $InfosMembre['utilisateur_biographie'])) ?>
	</fieldset>

	<div class="send">
		<input type="submit" name="submit" value="Modifier"/>
	</div>
</form>
