<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosBillet['version_titre']); ?></h1>
<?php if(!empty($InfosBillet['version_sous_titre'])){ ?>
<h2><?php echo htmlspecialchars($InfosBillet['version_sous_titre']); ?></h2>
<?php } ?>

<p>
	Vous vous apprêtez à répondre à une proposition de billet. Si jamais
	vous le validez, il n'apparaitra pas immédiatement sur la page d'accueil
	mais sera mis « En cours de préparation ».
</p>

<p class="gras centre">
<?php if(verifier('blog_voir_historique')){ ?>
<a href="validation-<?php echo $_GET['id']; ?>.html">Voir l'historique de validation de ce billet</a><br />
<?php } ?>
<a href="#reponse">Valider ou refuser le billet</a>
</p>

<span class="citation">Citation : <?php echo htmlspecialchars($InfosValidation['utilisateur_pseudo']); ?></span>
<div class="citation2"><?php echo $view['messages']->parse($InfosValidation['valid_commentaire']); ?></div>

<br /><hr />

<?php echo $view->render('ZcoBlogBundle::_billet.html.php',
	array(
		'verifier_editer' => $verifier_editer,
		'verifier_devalider' => $verifier_devalider,
		'verifier_supprimer' => $verifier_supprimer,
		'InfosBillet' => $InfosBillet,
		'Auteurs' => $Auteurs,
	)) ?>

<hr />

<fieldset>
	<legend id="reponse">Répondre à une proposition de billet</legend>
	<form method="post" action="">
		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>

		<input type="radio" name="decision" value="<?php echo DECISION_VALIDER; ?>" /> <span class="vertf">Valider</span><br />
		<input type="radio" name="decision" value="<?php echo DECISION_REFUSER; ?>" /> <span class="rouge">Refuser</span><br /><br />

		<label for="texte">Explication envoyée par mail :</label><br />
		<textarea name="texte" id="texte" rows="8" cols="80"></textarea><br />
		<p><em>zCode désactivé. xHTML désactivé. Les retours à la ligne seront automatiquement convertis.</em></p>

		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</form>
</fieldset>
