<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un commentaire</h1>

<form method="post" action="">
<fieldset><legend>Ajouter un commentaire</legend>
<?php echo $view->render('::zform.html.php'); ?>

<div class="send">
	<input type="submit" name="submit" value="Envoyer" />
</div>
</fieldset>
</form>