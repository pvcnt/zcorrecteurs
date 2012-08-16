<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
	'tab' => 'email', 
	'id' => $own ? null : $user->getId(),
)) ?>

<h1>
	Modifier <?php echo $own ? 'mon ' : 'l\'' ?>adresse courriel
	<?php if ($own): ?>
		<small>Changez l'adresse à laquelle nous vous écrivons.</small>
	<?php else: ?>
		de <?php echo htmlspecialchars($user->getUsername()) ?>
	<?php endif ?>
</h1>


<?php if ($own): ?>
<div class="alert alert-info">
	Vous recevrez un mail à l'adresse indiquée. Vous devrez alors valider votre nouvelle adresse
	en cliquant sur le lien contenu dans ce mail.
</div>
<?php endif ?>

<form action="" method="post" class="form-horizontal">
	<?php echo $view['form']->widget($form) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Modifier le mot de passe" />
	</div>
</form>
