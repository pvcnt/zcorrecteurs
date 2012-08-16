<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
	'tab' => 'password', 
	'id' => $own ? null : $user->getId(),
)) ?>

<h1>
	Modifier <?php echo $own ? 'mon' : 'le' ?> mot de passe
	<?php if ($own): ?>
		<small>Changez votre mot de passe actuel.</small>
	<?php else: ?>
		de <?php echo htmlspecialchars($user->getUsername()) ?>
	<?php endif ?>
</h1>

<form action="" method="post" class="form-horizontal">
	<?php echo $view['form']->widget($form) ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Modifier le mot de passe" />
	</div>
</form>
