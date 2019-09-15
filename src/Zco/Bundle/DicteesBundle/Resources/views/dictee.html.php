<?php $view->extend('::layouts/default.html.php') ?>

<?php $view['slots']->start('meta') ?>
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="zcorrecteurs" />
<meta name="twitter:url" content="<a href="<?php echo $view['router']->url('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)]) ?>">" />
<meta name="twitter:description" content="<?php echo htmlspecialchars(strip_tags($Dictee->description)) ?>" />
<meta name="twitter:title" content="<?php echo htmlspecialchars($Dictee->titre) ?>" />
<?php if ($Dictee->icone): ?>
	<meta name="twitter:image" content="<?php echo URL_SITE ?>/<?php echo htmlspecialchars($Dictee->icone) ?>" />
<?php endif ?>
<?php $view['slots']->stop() ?>

<h1><?php echo htmlspecialchars($Dictee->titre) ?></h1>
<?php echo $view->render('ZcoDicteesBundle::_dictee.html.php', compact('Dictee', 'DicteeEtats', 'DicteeDifficultes')) ?>

<h2>Jouer</h2>
<p>	Un fichier audio contenant la dictée vous est proposé, vous devez retranscrire son contenu fidèlement
	et avec le moins de fautes possible. Chaque faute vous fera perdre un point, sur une base de 20
	(la note minimale est zéro, pas de nombres négatifs).<br />
	Vous êtes libre d'arrêter ou de reprendre la lecture à tout instant, en cliquant sur le bouton pause.
</p>
<p>	Si une partie du fichier audio vous semble peu claire, merci de nous en faire part
	<a href="/forum/">sur le forum</a>,
	nous nous efforcerons de fournir une lecture la plus claire possible.<br/>
	Bonne chance !
</p>

<p class="rmq information" style="margin-top: 20px">Le temps nécessaire pour faire cette dictée est estimé à
<?php echo $Dictee->temps_estime ?> minutes.</p>

<div style="clear: right"></div>

<?php if($Dictee->etat != DICTEE_VALIDEE): ?>
<?php echo $view->render('ZcoDicteesBundle::_audio.html.php', compact('Dictee')) ?>
<p style="margin-top: 40px;" class="rmq erreur">
Cette dictée n'est pas publique, vous ne pouvez donc pas la jouer.
</p>
<?php else: ?>
<form action="corriger-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre) ?>.html" method="post">
	<?php if($Dictee->indications): ?>
	<fieldset>
		<legend>Indications</legend>
		<p><?php echo $view['messages']->parse($Dictee->indications, array(
		    'core.anchor_prefix' => $Dictee['id'],
		    'files.entity_id' => $Dictee['id'],
		    'files.entity_class' => 'Dictee',
			'files.part' => 3,
		)) ?></p>
	</fieldset>
	<?php endif ?>
	<fieldset>
		<legend>Dictée</legend>
		<?php echo $view->render('ZcoDicteesBundle::_audio.html.php', compact('Dictee')) ?>

		<label for="texte">Votre réponse :</label>
		<textarea id="texte" name="texte" rows="10" style="width: 98%" spellcheck="false"></textarea>
	</fieldset>
	<p class="centre">
	<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>" />
	<input type="submit" value="Corriger" /></p>
</form>
<?php endif ?>