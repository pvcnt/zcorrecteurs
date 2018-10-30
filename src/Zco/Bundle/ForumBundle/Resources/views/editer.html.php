<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosMessage['sujet_titre']); ?></h1>

<form action="" method="post">
	<fieldset>
		<legend>Modifier un message</legend>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="2" />
		</div>

		<?php if(verifier('editer_sujets', $InfosMessage['sujet_forum_id']) || (verifier('editer_ses_sujets', $InfosMessage['sujet_forum_id']) && $_SESSION['id'] == $InfosMessage['sujet_auteur'])){ ?>
		<label for="titre">Titre : </label>
		<input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($InfosMessage['sujet_titre']); ?>" size="35" tabindex="1" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" value="<?php echo htmlspecialchars($InfosMessage['sujet_sous_titre']); ?>" size="35" tabindex="2" /><br />

		<?php /*<label for="tags">Mots-clés : </label>
		<input type="text" name="tags" id="tags" value="<?php echo implode(array_values($Tags), ', '); ?>" size="35" tabindex="3" />
		<br />*/ ?><br />
		<?php } ?>

		<label for="texte">Contenu du message :</label>
		<?php echo $view->render('::zform.html.php', array(
			'upload_utiliser_element' => true, 
			'upload_id_formulaire' => $InfosMessage['message_sujet_id'],
			'texte' => $InfosMessage['message_texte'],
		)) ?>

		<?php
		if($InfosMessage['sujet_annonce'])
		{
			$checked_annonce = 'checked="checked"';
		}
		else
		{
			$checked_annonce = '';
		}
		if($InfosMessage['sujet_ferme'])
		{
			$checked_ferme = 'checked="checked"';
		}
		else
		{
			$checked_ferme = '';
		}
		if($InfosMessage['sujet_resolu'])
		{
			$checked_resolu = 'checked="checked"';
		}
		else
		{
			$checked_resolu = '';
		}
		if(verifier('masquer_avis_edition', $InfosMessage['sujet_forum_id']))
		{
		?>
		<p>
		<label for="aucun_message_edit">Ne pas afficher l'avis d'édition : </label><input type="checkbox" id="aucun_message_edit" name="aucun_message_edit" /><br />
		<?php
		}
		if(verifier('epingler_sujets', $InfosMessage['sujet_forum_id']))
		{
		?>
		<label for="annonce">Sujet épinglé :</label> <input type="checkbox" name="annonce" id="annonce" <?php echo $checked_annonce; ?> /><br />
		<?php
		}
		if(verifier('fermer_sujets', $InfosMessage['sujet_forum_id']))
		{
		?>
		<label for="ferme">Sujet fermé :</label> <input type="checkbox" name="ferme" id="ferme" <?php echo $checked_ferme; ?> /><br />
		<?php
		}
		if(verifier('resolu_sujets', $InfosMessage['sujet_forum_id']))
		{
		?>
		<label for="resolu">Sujet résolu :</label> <input type="checkbox" name="resolu" id="resolu" <?php echo $checked_resolu; ?> />
		</p>
		<?php
		}
		?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="3" />
		</div>
	</fieldset>
</form>

<?php include(__DIR__.'/revue_sujet.html.php'); ?>

<p class="centre">
	<strong>Retour <a href="sujet-<?php echo $InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']); ?>.html">au sujet "<?php echo htmlspecialchars($InfosMessage['sujet_titre']); ?>"</a>
	ou <a href="<?php echo FormateURLCategorie($InfosMessage['sujet_forum_id']); ?>">au forum "<?php echo htmlspecialchars($InfosForum['cat_nom']); ?>"</a></strong>
</p>
