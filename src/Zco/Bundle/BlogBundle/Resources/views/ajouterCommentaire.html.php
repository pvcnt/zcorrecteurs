<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></h1>

<?php if(!empty($InfosBillet['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?></h2>
<?php } ?>

<form action="" method="post">
	<fieldset>
		<legend>Ajouter un commentaire</legend>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="2" />
		</div>

		<?php echo $view->render('::zform.html.php', array(
			'tabindex' => 1, 
			'upload_utiliser_element' => true, 
			'upload_id_formulaire' => $_GET['id'],
			'texte' => $texte_zform,
		)) ?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="3" />
		</div>
	</fieldset>
</form>

<?php echo $view->render('ZcoBlogBundle::_revue_commentaires.html.php', array('ListerCommentaires' => $ListerCommentaires, 'InfosBillet' => $InfosBillet)) ?>
