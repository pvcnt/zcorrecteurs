<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo $title ?></h1>

<?php if (!empty($description)): ?>
	<p><?php echo $description; ?></p>
<?php endif; ?>

<form method="post" action="">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>" />
	<fieldset>
		<legend><?php echo $title ?></legend>
		<?php echo $message ?>

		<div class="centre">
			<input type="submit" name="yes" value="Oui" />
			<input type="submit" name="no" value="Non" />
		</div>
	</fieldset>
</form>