<?php $view->extend('::layouts/default.html.php') ?>

<h1>
	<?php echo htmlspecialchars($sondage['nom']) ?>

	<?php if (verifier('sondages_editer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_editer_siens'))){ ?>
	<a href="modifier-<?php echo $sondage['id'] ?>.html" title="Modifier le sondage">
		<img src="/img/editer.png" alt="Modifier" />
	</a>
	<?php } if (verifier('sondages_supprimer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_supprimer_siens'))){ ?>
	<a href="supprimer-<?php echo $sondage['id'] ?>.html" title="Supprimer le sondage">
		<img src="/img/supprimer.png" alt="Supprimer" />
	</a>
	<?php } ?>
</h1>

<?php if (!$sondage->estOuvert()){ ?>
<div class="rmq information">
	<img src="/bundles/zcoforum/img/cadenas.png" alt="" />
	Ce sondage a été fermé <?php echo dateformat($sondage['date_fin'], MINUSCULE) ?>,
	le vote n'est donc plus possible.
</div>
<?php } ?>

<p><?php echo $view['messages']->parse($sondage['description']) ?></p>

<?php if ($sondage['nb_questions'] == 0){ ?>
<p class="centre">Ce sondage ne comporte encore aucune question.</p>
<?php } else{ ?>
<fieldset class="UI_box">
	<h2 id="question">
		Question
		<?php if ($sondage['nb_questions'] > 1) echo 'n&nbsp;<sup>o</sup>'.($index+1).'/'.$sondage['nb_questions'] ?>

		<?php if (verifier('sondages_editer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_editer_siens'))){ ?>
		<a href="modifier-question-<?php echo $question['id']; ?>.html" title="Modifier la question">
			<img src="/img/editer.png" alt="Modifier" />
		</a>
		<a href="supprimer-question-<?php echo $question['id']; ?>.html" title="Supprimer la question">
			<img src="/img/supprimer.png" alt="Supprimer" />
		</a>
		<?php } ?>
	</h2>

	<p>
		<?php echo $view['messages']->parse($question['nom']) ?>

		<?php /* Si on affiche les résultats */
		if ((!$sondage->estOuvert() || $a_vote || !verifier('sondages_voter')) && (verifier('sondages_voir_resultats') || $question['resultats_publics'])){ ?>
		<em>(<?php echo $question['nb_votes']+$question['nb_blanc'] ?> vote<?php echo pluriel($question['nb_votes']+$question['nb_blanc']); ?>)</em>
		<?php } ?>
	</p>

	<?php /* Si on affiche les résultats */
	if ((!$sondage->estOuvert() || $a_vote || !verifier('sondages_voter')) && (verifier('sondages_voir_resultats') || $question['resultats_publics'])){ ?>
	<h2>Réponses</h2>

	<?php if (!$question['libre']){ ?>
	<dl>
		<?php foreach ($reponses as $reponse){ $pourcentage = ($question['nb_votes']+$question['nb_blanc']) > 0 ? ($reponse['nb_votes'] / ($question['nb_votes']+$question['nb_blanc'])) * 100 : 0; ?>
		<dt>
			<?php echo htmlspecialchars($reponse['nom']) ?>
			(<?php echo $reponse['nb_votes'] ?> vote<?php echo pluriel($reponse['nb_votes']) ?>)
		</dt>
		<dd>
			<img src="/bundles/zcosondages/img/barre_gauche.png" alt="" /><img src="/bundles/zcosondages/img/barre_centre.png" alt="" style="width: <?php echo $taille_barre = round(($pourcentage * 400) / 100 , 0) ?>px; height: 8px;" /><img src="/bundles/zcosondages/img/barre_droite.png" alt="" />
			<?php echo $view['humanize']->numberformat($pourcentage, 0) ?> %
		</dd>
		<?php } ?>

		<?php $pourcentage = ($question['nb_votes'] + $question['nb_blanc']) > 0 ? ($question['nb_blanc'] / ($question['nb_votes'] + $question['nb_blanc'])) * 100 : 0;	?>
		<dt>Votes blancs (<?php echo $question['nb_blanc'] ?> vote<?php echo pluriel($question['nb_blanc']); ?>)</dt>
		<dd>
			<img src="/bundles/zcosondages/img/barre_gauche.png" alt="" /><img src="/bundles/zcosondages/img/barre_centre.png" alt="" style="width: <?php echo round(($pourcentage * 400) / 100 , 0) ?>px; height: 8px;" /><img src="/bundles/zcosondages/img/barre_droite.png" alt="" />
			<?php echo $view['humanize']->numberformat($pourcentage, 0) ?> %
		</dd>
	</dl>
	<?php } else{ ?>
	<?php if (count($votes) > 0){ ?>
	<table class="UI_items">
		<thead>
			<tr>
				<?php if (verifier('sondages_voir_votants')){ ?>
				<th style="width: 15%;">Pseudo</th>
				<?php } ?>
				<th style="width: 15%;">Date</th>
				<th style="width: 70%;">Message</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($votes as $i => $vote){ ?>
			<tr class="<?php echo $i % 2 ? 'odd' : 'even' ?>">
				<?php if (verifier('sondages_voir_votants')){ ?>
				<td><?php echo !empty($vote['utilisateur_id']) ? $vote->Utilisateur : 'Anonyme' ?></td>
				<?php } ?>
				<td class="centre"><?php echo dateformat($vote['date']) ?></td>
				<td><?php echo nl2br(htmlspecialchars($vote->TexteLibre['texte'])) ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } else{ ?>
	<p>Aucune réponse n'a été déposée.</p>
	<?php } } ?>

	<?php } /* S'il n'a pas encore voté et le peut */
	elseif ($sondage->estOuvert() && !$a_vote && verifier('sondages_voter')){ ?>
	<form method="post" action="voter-<?php echo $question['id'] ?>.html">
		<?php if (!$question['libre']){ ?>
		<h2>Réponses</h2>

		<?php if ($question['nb_max_choix'] > 1){ ?>
		<p>Choisissez de <?php echo $question['nb_min_choix'] ?> à <?php echo $question['nb_max_choix'] ?> réponses.</p>
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
		<h2>Réponse libre</h2>

		<div class="centre">
			<textarea name="reponse" id="reponse" style="width: 700px; height: 150px;"></textarea>
		</div>

		<div class="send">
			<input type="submit" name="voter" value="Envoyer ma réponse" />
			<?php if (!$question['obligatoire']){ ?>
			<input type="submit" name="blanc" value="Ne pas répondre" />
			<?php } ?>
		</div>
		<?php } ?>
	</form>
	<?php } else{ ?>
	<?php if (!$question['libre']){ ?>
	<h2>Réponses</h2>
	<dl>
		<?php foreach ($reponses as $reponse){ ?>
		<dt><?php echo htmlspecialchars($reponse['nom']) ?></dt>
		<?php } ?>
	</dl>
	<?php } else{ ?>
	<h2>Réponse libre</h2>
	<p>Cette question admet une réponse sous forme d'un texte libre.</p>
	<?php } } ?>

	<?php if ($sondage['nb_questions'] > 1){ ?><br />
	<div class="centre">
		<form method="post" action="">
			<?php if ($index > 0){ ?>
			<a href="sondage-<?php echo $sondage['id']; ?>-<?php echo $questions[$index-1]['id']; ?>.html" title="Question précédente">&larr; Précédente</a>
			<?php } ?>
			<select name="saut_rapide" onchange="document.location='sondage-<?php echo $sondage['id'] ?>-'+this.value+'.html';">
				<?php foreach ($questions as $i => $quest){ ?>
					<option value="<?php echo $quest['id'] ?>"<?php if ($quest['id'] == $question['id']) echo ' selected="selected"'; ?>>
						Question n° <?php echo $i+1 ?>
					</option>
				<?php } ?>
			</select>
			<noscript><input type="submit" value="Aller" /></noscript>

			<?php if ($index < ($sondage['nb_questions'] - 1)){ ?>
			<a href="sondage-<?php echo $sondage['id']; ?>-<?php echo $questions[$index+1]['id']; ?>.html" title="Question suivante">Suivante &rarr;</a>
			<?php } ?>
		</form>
	</div>
	<?php } ?>
</fieldset>
<?php } ?>

<div class="UI_box gras centre" style="margin-top: 20px;">
	<a href="index.html">Retour à la liste des sondages</a><br />
	<a href="/">Retour à l'accueil du site</a>
</div>
