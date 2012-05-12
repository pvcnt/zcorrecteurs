<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view['form']->errors($form) ?>

<h1>Se connecter</h1>

<p>En vous connectant, vous pourrez rédiger des messages sur le forum, proposer des billets et bien plus encore !</p>

<p><a href="/aide/page-2-identifiants-et-connexion.html">
    <img src="/img/misc/aide.png" alt="" />
    Plus d'informations sur la connexion.
</a></p>

<form method="post" action="<?php echo $view['router']->generate('zco_user_session_login') ?>" class="form-horizontal" <?php echo $view['form']->enctype($form) ?>>
	<fieldset>
		<legend>Identifiants de connexion</legend>
		<?php echo $view['form']->row($form['pseudo']); ?>
		<?php echo $view['form']->row($form['password']); ?>
		<?php echo $view['form']->row($form['remember']); ?>
		
		<?php echo $view['form']->rest($form); ?>
		
		<?php /*if($captcha){ ?><br />
    	<?php echo $view->render('ZcoCaptchaBundle::_image.html.php'); ?>
    	<label for="captcha">Recopier le code : </label>
    	<input type="text" name="captcha" id="captcha" />
    	<?php }*/ ?>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="Connexion" />
		</div>
	</fieldset>
</form>

<br />
<h2>Je ne suis pas inscrit</h2>
<p>
	Vous n'êtes pas encore inscrit ? Rendez-vous donc sur la 
	<a href="<?php echo $view['router']->generate('zco_user_session_register') ?>">page d'inscription</a> 
	pour vous enregistrer immédiatement.
</p>

<h2>J'ai perdu ou oublié mon mot de passe</h2>
<p>
	Vous pouvez faire une demande de nouveau mot de passe sur cette page : 
	<a href="<?php echo $view['router']->generate('zco_user_session_newPassword') ?>">demande de nouveau mot de passe</a>.
</p>

<?php $view['javelin']->initBehavior('autofocus', array('id' => 'utilisateur')) ?>