<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosCommentaire['version_titre']); ?></h1>

<?php if(!empty($InfosCommentaire['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosCommentaire['version_sous_titre']); ?></h2>
<?php } ?>

<form action="" method="post">
	<fieldset>
		<legend>Modifier un commentaire</legend>
		<div class="send">
			<input type="submit" name="submit" value="Envoyer" accesskey="s" tabindex="2" />
		</div>

		<?php echo $view->render('::zform.html.php', array(
			'upload_utiliser_element' => true,
			'upload_id_formulaire' => $InfosCommentaire['blog_id'],
			'texte' => $InfosCommentaire['commentaire_texte'],
			'tabindex' => 1,
		)) ?>

		<div class="send">
			<input type="submit" name="submit" value="Envoyer" accesskey="s" tabindex="3" />
		</div>
	</fieldset>
</form>

<?php echo $view->render('ZcoBlogBundle::_revue_commentaires.html.php', array(
	'ListerCommentaires' => $ListerCommentaires,
	'MPTotal' => $_SESSION['MPs'],
	'InfosBillet' => $InfosCommentaire,
)) ?>
