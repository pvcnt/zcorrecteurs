<?php $view->extend('::layouts/light.html.php') ?>

<h1>Retour d'expérience utilisateur</h1>

<form method="post" action="">
	<input type="hidden" name="captcha1" value="<?php echo $captcha1 ?>" />
	<input type="hidden" name="captcha2" value="<?php echo $captcha2 ?>" />
	
	<fieldset>
		<legend>Retour d'expérience</legend>
		
		<label for="contenu">Votre message :</label>
		<textarea name="contenu" id="contenu"><?php if (isset($_POST['contenu'])) echo htmlspecialchars($_POST['contenu']) ?></textarea><br />
		
		<?php if (!verifier('connecte')): ?>
		<label for="captcha">Combien font <?php echo $captcha1 ?> + <?php echo $captcha2 ?> ?</label>
		<input type="text" name="captcha" id="captcha" size="4" /><br />
		<?php endif; ?>
		
		<label for="email">Adresse courriel :</label>
		<input type="text" name="email" id="email" size="40" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']) ?>" /><br />
		<em>Saisissez une adresse courriel si vous nous autorisez à vous contacter pour avoir plus de détails.</em>
		
		<div class="send"><input type="submit" value="Envoyer" /></div>
	</fieldset>
</form>