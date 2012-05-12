<p class="gras centre"><?php echo htmlspecialchars($sondage['nom']) ?></p>

<p class="centre italique">
	<a href="/sondages/">Aller à la liste des sondages</a>

	<?php if ($question != false){ ?> -
	<a href="/sondages/sondage-<?php echo $sondage['id'] ?>-<?php echo rewrite($sondage['nom']) ?>.html">
		Aller à ce sondage
	</a>
	<?php } ?>
</p>

<?php if ($question != false){ ?>
<p>
	<?php echo $view['messages']->parse($question['nom']) ?>

	<?php /* Si on affiche les résultats */
	if ((!$sondage->estOuvert() || $a_vote || !verifier('sondages_voter')) && (verifier('sondages_voir_resultats') || $question['resultats_publics'])){ ?>
	<em>(<?php echo $question['nb_votes'] ?> vote<?php echo pluriel($question['nb_votes']) ?>)</em>
	<?php } ?>
</p>

<?php /* Si on affiche les résultats */
if ((!$sondage->estOuvert() || $a_vote || !verifier('sondages_voter')) && (verifier('sondages_voir_resultats') || $question['resultats_publics'])){ ?>
<dl>
	<?php foreach($reponses as $rep){ $pourcentage = $question['nb_votes'] > 0 ? 100 * $rep['nb_votes'] / $question['nb_votes'] : 0; ?>
	<dt><?php echo htmlspecialchars($rep['nom']) ?> (<?php echo $rep['nb_votes'] ?> vote<?php echo pluriel($rep['nb_votes']) ?>)</dt>
	<dd>
		<img src="/bundles/zcosondages/img/barre_gauche.png" alt="" /><img src="/bundles/zcosondages/img/barre_centre.png" alt="" style="width: <?php echo round(($pourcentage * 400) / 100) ?>px; height: 8px;" /><img src="/bundles/zcosondages/img/barre_droite.png" alt="" />
		<?php echo $view['humanize']->numberformat($pourcentage, 0) ?> %
	</dd>
	<?php } ?>

	<?php $pourcentage = ($question['nb_votes'] + $question['nb_blanc']) > 0 ? 100 * $question['nb_blanc'] / ($question['nb_votes'] + $question['nb_blanc']) : 0; ?>
	<dt>Votes blancs (<?php echo $question['nb_blanc']; ?> vote<?php echo pluriel($question['nb_blanc']); ?>)</dt>
	<dd>
		<img src="/bundles/zcosondages/img/barre_gauche.png" alt="" width="4" height="8" /><img src="/bundles/zcosondages/img/barre_centre.png" alt="" style="width: <?php echo round(($pourcentage * 400) / 100) ?>px; height: 8px;" /><img src="/bundles/zcosondages/img/barre_droite.png" alt="" width="4" height="8" />
		<?php echo $view['humanize']->numberformat($pourcentage, 0) ?> %
	</dd>
</dl>

<?php } /* S'il n'a pas encore voté et le peut */
elseif ($sondage->estOuvert() && !$a_vote && verifier('sondages_voter')){ ?>
<form method="post" action="/sondages/voter-<?php echo $question['id'] ?>.html">
	<?php if (!$question['libre']){ ?>
	<?php if ($question['nb_max_choix'] > 1){ ?>
	<p>De <?php echo $question['nb_min_choix'] ?> à <?php echo $question['nb_max_choix'] ?> choix.</p>
	<?php } ?>
	<dl>
		<?php foreach ($reponses as $reponse){ ?>
		<dt>
			<?php if ($question['nb_max_choix'] > 1){ ?>
			<input type="checkbox" name="reponse[]" value="<?php echo $reponse['id'] ?>" id="rep<?php echo $reponse['id'] ?>" />
			<?php } else{ ?>
			<input type="radio" name="reponse[]" value="<?php echo $reponse['id'] ?>" id="rep<?php echo $reponse['id'] ?>" />
			<?php } ?>
			<label for="rep<?php echo $reponse['id']; ?>" class="nofloat">
				<?php echo htmlspecialchars($reponse['nom']) ?>
			</label>
		</dt>
		<?php } ?>
	</dl><br />

	<div class="send">
		<input type="submit" name="voter" value="Voter" />
		<?php if (!$question['obligatoire']){ ?>
		<input type="submit" name="blanc" value="Blanc" />
		<?php } ?>
	</div>
	<?php } else{ ?>
	<div class="centre">
		<textarea name="reponse" id="reponse" style="width: 80%; height: 100px;"></textarea>
	</div>

	<div class="centre">
		<input type="submit" name="voter" value="Envoyer" />
		<?php if (!$question['obligatoire']){ ?>
		<input type="submit" name="blanc" value="Ne pas répondre" />
		<?php } ?>
	</div>
	<?php } ?>
</form>

<?php } /* S'il ne peut pas voter */ else{ ?>
<dl>
	<?php foreach ($reponses as $rep){ ?>
	<dt><?php echo htmlspecialchars($rep['nom']); ?></dt>
	<?php } ?>
</dl>
<?php } } else{ ?>
<p>Aucun sondage n'est en cours.</p>
<?php } ?>
