<?php $view->extend('::layouts/default.html.php') ?>

<h1>Nouveau sujet</h1>
<?php
if(!empty($_GET['trash']))
{
?>
<p class="UI_errorbox">
Vous êtes dans la corbeille !
</p>
<?php
}
?>

<?php if(!empty($InfosForum['cat_reglement'])) echo '<div class="reglement">'.$view['messages']->parse($InfosForum['cat_reglement']).'</div>'; ?>

<form action="" method="post">
	<fieldset>
		<legend>Nouveau sujet</legend>
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="5" />
		</div>

		<label for="titre">Titre : </label>
		<input type="text" name="titre" id="titre" value="<?php if(!empty($_POST['titre'])) echo htmlspecialchars($_POST['titre']); ?>" size="35" tabindex="1" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" value="<?php if(!empty($_POST['sous_titre'])) echo htmlspecialchars($_POST['sous_titre']); ?>" size="35" tabindex="1" /><br />

		<?php /*<label for="tags">Mots-clés : </label>
		<input type="text" name="tags" id="tags" size="35" tabindex="3" />
		<br />*/ ?><br />

		<label for="texte">Contenu du message :</label>
		<?php echo $view->render('::zform.html.php', array('upload_utiliser_element' => true, 'texte' => $texte_zform)); ?>
		
		<?php
		if(verifier('corbeille_sujets', $_GET['id']))
		{
			if(!empty($_GET['trash']))
			{
				$checked_corbeille = 'checked';
			}
			else
			{
				$checked_corbeille = '';
			}
		}
		?>
		<?php if(verifier('epingler_sujets', $_GET['id']) OR verifier('fermer_sujets', $_GET['id']) OR verifier('resolu_sujets', $_GET['id']) OR verifier('corbeille_sujets', $_GET['id']))
		{
		?>
		<p>
			<?php
			if(verifier('epingler_sujets', $_GET['id']))
			{
			?>
			<label for="annonce">Sujet épinglé :</label> <input type="checkbox" name="annonce" id="annonce" /><br />
			<?php
			}
			if(verifier('fermer_sujets', $_GET['id']))
			{
			?>
			<label for="ferme">Sujet fermé :</label> <input type="checkbox" name="ferme" id="ferme" /><br />
			<?php
			}
			if(verifier('resolu_sujets', $_GET['id']))
			{
			?>
			<label for="resolu">Sujet résolu :</label> <input type="checkbox" name="resolu" id="resolu" /><br />
			<?php
			}
			if(verifier('corbeille_sujets', $_GET['id']))
			{
			?>
			<label for="corbeille">Sujet dans la corbeille :</label> <input type="checkbox" name="corbeille" id="corbeille" <?php echo $checked_corbeille; ?> />
			<?php
			}
			?>
		</p>
		<?php
		}
		?>

		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" tabindex="6" />
		</div>
	</fieldset>
</form>