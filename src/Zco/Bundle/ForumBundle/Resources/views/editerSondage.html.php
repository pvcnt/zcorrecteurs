<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modification d'un sondage</h1>
<h2><?php echo htmlspecialchars($InfosSondage['sondage_question']); ?></h2>

<p>
	Bienvenue dans l'interface de modification de sondages. Vous pouvez
	directement modifier la question et les réponses dans les champs ci-dessous.<br />
	Pour supprimer une réponse, rien de plus simple, laissez le champ vide. Pour
	ajouter une réponse, remplissez un nouveau champ !<br />
	Vous pouvez ensuite choisir de réinitialiser les votes du sondage si vous
	le souhaitez (ajout d'une nouvelle réponse, modification complète du sens
	d'une réponse, etc.).
</p>

<form action="" method="post">
	<fieldset>
		<legend>Modifier un sondage</legend>
		<div class="send">
			<input type="submit" name="send" value="Modifier" accesskey="s" tabindex="50" /><br /><br />
		</div>

		<label for="question" style="width:100px;">Question :</label>
		<input type="text" name="question" id="question"
			value="<?php echo htmlspecialchars($InfosSondage['sondage_question']);
			?>" size="60" tabindex="199" />
		<div id="sondage_reponses">
		<?php
		$tabindex = 200;
		foreach($ListerQuestions as &$reponse)
		{
			$tabindex++;
			?>
			<div>
			<label	for="sdg_reponse<?php echo $tabindex; ?>"
				style="width:100px;" >
				Réponse <?php echo $tabindex - 200; ?> :
			</label>
			<input	type="text"
				name="reponses[]"
				id="sdg_reponse<?php echo $tabindex; ?>"
				size="60"
				value="<?php echo htmlspecialchars($reponse['choix_texte']); ?>"
				tabindex="<?php echo $tabindex; ?>"/>
			</div>
		<?php }
		for($i = 0; $i < 4; $i++):
			$tabindex++;
			?>
			<div>
			<label	for="sdg_reponse<?php echo $tabindex; ?>"
				style="width:100px;" >
				Réponse <?php echo $tabindex - 200; ?> :
			</label>
			<input	type="text"
				name="reponses[]"
				id="sdg_reponse<?php echo $tabindex; ?>"
				size="60"
				tabindex="<?php echo $tabindex; ?>"/>
			</div>
		<?php endfor; ?>
		</div>

		<label for="reinitialiser_votes" style="width:100px;">Réinitialier les votes :</label>
		<input type="checkbox" name="reinitialiser_votes" id="reinitialiser_votes" />

		<div class="send">
			<br /><input type="submit" name="send" value="Modifier" accesskey="s" tabindex="51" />
		</div>
	</fieldset>
</form>

<?php $view['javelin']->initBehavior('forum-poll-form') ?>

<p class="centre">
	<strong>Retour <a href="sujet-<?php echo $_GET['id'].'-'.rewrite($InfosSondage['sujet_titre']); ?>.html">au sujet « <?php echo htmlspecialchars($InfosSondage['sujet_titre']); ?> »</a>
	ou <a href="<?php echo FormateURLCategorie($InfosSondage['cat_id']); ?>">au forum « <?php echo $InfosSondage['cat_nom']; ?> »</a></strong>
</p>
