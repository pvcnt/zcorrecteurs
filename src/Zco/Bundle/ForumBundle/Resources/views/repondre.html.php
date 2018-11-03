<?php $view->extend('::layouts/default.html.php') ?>

<?php if($antigrilled){ ?>
<div class="UI_errorbox">
	Quelqu'un a ajouté un message à ce sujet avant que vous ne postiez le vôtre. Vous pouvez le lire dans la revue du sujet au
	bas de cette page.<br />
	Si vous le souhaitez, postez votre message en recliquant sur le bouton <em>Envoyer</em>.
</div>
<?php } ?>

<h1><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></h1>

<form action="" method="post">
	<fieldset>
		<legend>Ajout d'une réponse</legend>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="3" />
		</div>

		<?php if(verifier('editer_sujets', $InfosSujet['sujet_forum_id']) || (verifier('editer_ses_sujets', $InfosSujet['sujet_forum_id']) && $_SESSION['id'] == $InfosSujet['sujet_auteur'])){ ?>
		<label for="titre">Titre : </label>
		<input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?>" size="35" tabindex="1" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" value="<?php echo htmlspecialchars($InfosSujet['sujet_sous_titre']); ?>" size="35" tabindex="1" />
		<br /><br />
		<?php } ?>

		<label for="texte">Contenu du message :</label>
		<?php echo $view->render('::zform.html.php', array(
			'upload_utiliser_element' => true,
			'upload_id_formulaire' => $_GET['id'],
			'texte' => $texte_zform,
		)); ?>

	<p>
<?php
if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']))
{
	if($InfosSujet['sujet_annonce'])
	{
		$checked_annonce = 'checked="checked"';
	}
	else
	{
		$checked_annonce = '';
	}
}
if(verifier('fermer_sujets', $InfosSujet['sujet_forum_id']))
{
	if($InfosSujet['sujet_ferme'])
	{
		$checked_ferme = 'checked="checked"';
	}
	else
	{
		$checked_ferme = '';
	}
}
if( (verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id']) AND $InfosSujet['sujet_auteur'] == $_SESSION['id']) OR verifier('resolu_sujets', $InfosSujet['sujet_forum_id']))
{
	if($InfosSujet['sujet_resolu'])
	{
		$checked_resolu = 'checked="checked"';
	}
	else
	{
		$checked_resolu = '';
	}
}
if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']) AND $InfosSujet['sujet_corbeille'])
{
	$checked_corbeille = 'checked="checked"';
}
else
{
	$checked_corbeille = '';
}
?>
	<?php
	if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		<label for="annonce">Sujet épinglé :</label> <input type="checkbox" name="annonce" id="annonce" <?php echo $checked_annonce; ?> /><br />
	<?php
	}
	if(verifier('fermer_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		<label for="ferme">Sujet fermé :</label> <input type="checkbox" name="ferme" id="ferme" <?php echo $checked_ferme; ?> /><br />
	<?php
	}
	if( (verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id']) AND $InfosSujet['sujet_auteur'] == $_SESSION['id']) OR verifier('resolu_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		<label for="resolu">Sujet résolu :</label> <input type="checkbox" name="resolu" id="resolu" <?php echo $checked_resolu; ?> /><br />
	<?php
	}
	if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']))
	{
	?>
		<label for="corbeille">Sujet dans la corbeille :</label> <input type="checkbox" name="corbeille" id="corbeille" <?php echo $checked_corbeille; ?> /><br />
	<?php
	}
	?>
	</p>

		<div class="cleaner">&nbsp;</div>
		<div class="cleaner">&nbsp;</div>
		<div class="cleaner">&nbsp;</div>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="4" />
		</div>
	</fieldset>
</form>

<?php include(__DIR__.'/revue_sujet.html.php'); ?>