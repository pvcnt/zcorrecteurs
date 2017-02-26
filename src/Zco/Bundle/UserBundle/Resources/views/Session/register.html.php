<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view['form']->errors($form) ?>

<h1>Inscription à zCorrecteurs.fr</h1>

<p>
	Remplissez le formulaire ci-dessous pour effectuer votre inscription sur ce
	site. Vous devrez ensuite valider votre compte en suivant un lien dans le
	mail qui vous sera envoyé. Vous aurez alors 24 h pour effectuer cette
	opération. Le compte sera sinon détruit automatiquement, pour vous permettre
	de vous réinscrire.</p>
<p>
	En cas de problème quelconque, vous pouvez 
	<a href="<?php echo $view['router']->path('zco_about_contact', array('objet' => 'Inscription')) ?>">nous joindre</a>.
</p>

<form method="post" action="<?php echo $view['router']->path('zco_user_session_register') ?>" class="form-horizontal" <?php echo $view['form']->enctype($form) ?>>
	<div class="row-fluid">
		<div class="span6">
			<fieldset>
				<legend>Informations sur votre compte</legend>
				<?php echo $view['form']->row($form['username']) ?>
				<div id="username-available" style="margin-bottom: 18px; text-align: center;"></div>
				<?php echo $view['form']->row($form['rawPassword']) ?>
				<?php echo $view['form']->row($form['email']) ?>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset>
				<legend>Confirmation anti-spam</legend>
				<?php echo $view->render('ZcoCaptchaBundle::_image.html.php'); ?>
				<div class="control-group">
					<label for="captcha" class="control-label">Recopiez le code</label>
					<div class="controls">
						<input type="text" name="captcha" id="captcha" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	
	<?php echo $view['form']->rest($form) ?>
	
	<p class="good">
		Vous disposez d’un droit d’accès, de modification, de rectification et 
		de suppression sur vos données conformément à la loi.
		<a href="<?php echo $view['router']->url('zco_legal_mentions') ?>">En savoir plus.</a>
	</p>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="inscrip_submit" value="M’inscrire sur zCorrecteurs.fr" />
	</div>
</form>

<?php $view['javelin']->initBehavior('validate-value', array(
	'id'        => $form['username']->vars['id'],
	'result_id' => 'username-available',
	'postVar'   => 'pseudo',
	'callback'  => $view['router']->path('zco_user_api_validateUsername'),
)) ?>