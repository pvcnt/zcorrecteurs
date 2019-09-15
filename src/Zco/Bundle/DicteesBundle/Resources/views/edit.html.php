<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier une dictée</h1>
<form action="" method="post" enctype="multipart/form-data">
<?php echo $Form; ?>
<p class="centre">
	<input type="submit" value="Sauvegarder" name="sauvegarder" />
</p>
</form>
