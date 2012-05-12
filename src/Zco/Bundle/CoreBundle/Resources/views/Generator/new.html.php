<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo $config['new']['title'] ?></h1>

<?php if(!empty($config['new']['description'])): ?>
	<p><?php echo $config['new']['description'] ?></p>
<?php endif; ?>

<form method="post" action="">
	<div class="centre"><?php include(dirname(__FILE__).'/_save.php'); ?></div>

	<p class="gras">
		<?php if($action == 'edit'): ?>
		<a href="<?php echo str_replace('%id%', $object['id'], $config['actions']['_delete']['route']) ?>">
			<img src="/bundles/zcocore/img/generator/button_delete.png" alt="" />
			<?php echo $config['actions']['_delete']['label'] ?>
		</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>

		<a href="<?php printf($config['actions']['_list']['route'], 1); ?>">
			<img src="/bundles/zcocore/img/generator/list.png" alt="" />
			<?php echo $config['actions']['_list']['label']; ?>
		</a>
	</p>

	<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />

	<?php echo $form; ?>

	<div class="centre"><?php include(dirname(__FILE__).'/_save.php'); ?></div>
</form>
