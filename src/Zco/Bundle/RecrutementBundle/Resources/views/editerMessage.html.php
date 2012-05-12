<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un commentaire</h1>

<form method="post" action="">
<fieldset><legend>Modifier un commentaire</legend>
<?php echo $view->render('::zform.html.php', array('texte' => $InfosCommentaire['commentaire_texte'])) ?>

<div class="send">
	<input type="submit" name="submit" value="Envoyer" />
</div>
</fieldset>
</form>
