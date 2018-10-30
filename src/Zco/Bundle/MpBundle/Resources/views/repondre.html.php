<?php $view->extend('::layouts/default.html.php') ?>

<?php if($nouveauMessage): ?>
	<div class="UI_errorbox">
	Quelqu'un a ajouté une réponse à ce message privé avant que vous ne postiez la
	vôtre. Vous pouvez la lire dans la revue du message au bas de cette page.
	<br />
	Si vous le souhaitez, postez votre réponse en recliquant
	sur le bouton <em>Envoyer</em>.
	</div>
<?php endif ?>

<h1>Ajouter une réponse</h1>
<fieldset>
<legend>Ajout d'une réponse</legend>
<form action="" method="post">
		<div class="send">
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>

		<?php echo $view->render('::zform.html.php'); ?>



		<div class="cleaner">&nbsp;</div>
		<?php
		if(verifier('mp_fermer'))
		{
			echo '<p><label for="ferme">MP fermé : </label><input type="checkbox" name="ferme" id="ferme" value="ferme"';
			if($InfoMP['mp_ferme'])
			{
				echo ' checked="checked"';
			}
			echo ' /></p>';
		}
		?>

		<div class="send">
			<input type="hidden"
			       name="dernier_message"
			       value="<?php echo $InfoMP['mp_dernier_message_id'] ?>"
			/>
			<input type="submit" name="send" value="Envoyer" accesskey="s" />
		</div>
	</form>
</fieldset>

<p class="gras centre">
	Retour <a href="lire-<?php echo $InfoMP['mp_id'] ?>-<?php echo rewrite($InfoMP['mp_titre']) ?>.html">
		au MP <em><?php echo htmlspecialchars($InfoMP['mp_titre']) ?></em>
	</a>
	ou
	<a href="index.html">à la liste des MP</strong></a>
</p>


<?php include(__DIR__.'/_revue_mp.html.php'); ?>
