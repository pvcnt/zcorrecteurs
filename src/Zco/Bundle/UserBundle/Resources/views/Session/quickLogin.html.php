<form method="post" action="<?php echo $view['router']->path('zco_user_session_login') ?>" <?php echo $view['form']->enctype($form) ?>>
	<?php echo $view['form']->widget($form['pseudo'], array('attr' => array('placeholder' => 'Pseudonyme'))) ?>
	<?php echo $view['form']->widget($form['password'], array('attr' => array('placeholder' => 'Mot de passe'))) ?>
	<?php echo $view['form']->rest($form); ?>
	<input type="submit" class="btn btn-primary" value="Se connecter" />
</form>