<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>

<p>Vous vous apprêtez à terminer la correction d'un tutoriel. Prenez un moment pour vérifier les informations ci-dessous avant de valider la fin de correction.<br />
<strong>Merci d'<a href="commentaires-<?php echo $_GET['id']; ?>.html?cid">éditer les commentaires</a> si vous voulez changer quelque chose.</strong></p>

<fieldset>
	<legend>Terminer la correction du tutoriel</legend>
	<form method="post" action="">
		<span class="citation">Commentaires à l'auteur : </span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($commentaire); ?></div><br />

		<span class="citation">Commentaires au validateur : </span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($commentaire_valido); ?></div><br />

		<span class="citation">Commentaires aux zCorrecteurs : </span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($commentaire2); ?></div><br />

		<label for="confidentialite">Cacher mon pseudo à l'auteur : </label> <?php if($confidentialite) echo 'Oui'; else echo 'Non'; ?><br />

		<?php if(!$s['recorrection_id']){ ?>
		<label for="recorrection">Recorrection nécessaire : </label> <input type="checkbox" name="recorrection" id="recorrection" checked="checked" /><br />
		<?php } ?>

		<div class="send">
			<input type="submit" value="Correction terminée !" name="correction" />
		</div>
	</form>
</fieldset>
