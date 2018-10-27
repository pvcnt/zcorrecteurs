<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
    'tab' => 'preferences',
    'id' => $own ? null : $user->getId(),
)) ?>

<h1>
	Modifier <?php echo $own ? 'mes' : 'les' ?> préférences
	<?php if ($own): ?>
		<small>Changez la façon dont vous naviguez sur le site.</small>
	<?php else: ?>
		de <?php echo htmlspecialchars($user->getUsername()) ?>
	<?php endif ?>
</h1>

<form action="" method="post" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	
	<div class="control-group">
		<label class="control-label">Notifications</label>
		<div class="controls">
			<?php echo $view['form']->row($form['email_on_mp'], array('style' => 'checkbox')) ?>
		</div>
	</div>
    <?php echo $view['form']->row($form['time_difference'], array('widget_attr' => array('class' => 'input-xxlarge'))) ?>
    <?php echo $view['form']->rest($form) ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Modifier les préférences" />
	</div>
</form>
