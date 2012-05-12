<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Changement de pseudonyme</h1>

<p>
	Vous vous apprêtez à demander un changement de pseudonyme. Nous vous 
	rappelons qu'il faut éviter de le faire trop souvent, sous peine de refus.
	À force, on s'y perd. Cela est donc soumis à validation d'un administrateur. 
	Merci de votre compréhension.
</p>

<form method="post" action="<?php echo $view['router']->generate('zco_user_newPseudo') ?>" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	<?php echo $view['form']->row($form['newUsername']) ?>
	<?php echo $view['form']->row($form['reason']) ?>
	<?php if (isset($form['autoValidated'])): ?>
		<?php echo $view['form']->row($form['autoValidated']) ?>
	<?php endif ?>
	<?php echo $view['form']->rest($form) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>

<?php $view['javelin']->initBehavior('validate-value', array(
	'id'       => $form['newUsername']->get('id'),
	'postVar'  => 'pseudo',
	'callback' => $view['router']->generate('zco_user_api_validateUsername'),
)) ?>