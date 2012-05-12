<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un sondage</h1>

<h2>Modifier les propriétés du sondage</h2>
<form method="post" action="">
	<fieldset>
		<legend>Informations sur le sondage</legend>

		<table class="UI_wrapper">
		<tr><td>
			<label for="nom">Nom du songage : </label>
			<input type="text" tabindex="1" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($sondage['nom']) ?>" /><br />

			<label for="texte">Description : </label>
			<a href="#" onclick="$('row_description').slide(); return false;">Modifier la description</a>
		</td>
		<td>
			<label for="date_debut">Date de début : </label>
			<?php echo $view->get('widget')->dateTimePicker('date_debut', $sondage['date_debut']) ?><br />

			<label for="date_fin">Date de fin : </label>
			<?php echo $view->get('widget')->dateTimePicker('date_fin', $sondage['date_fin'], array('allowEmpty' => true)) ?>
			<em>Laisser vide pour jamais.</em><br />

			<label for="ouvert">Sondage visible : </label>
			<input type="checkbox" name="ouvert" id="ouvert"<?php if ($sondage['ouvert']) echo 'checked="checked"' ?> />
		</td></tr>
		</table>

		<div id="row_description">
			<?php echo $view->render('::zform.html.php', array('id' => 2, 'texte' => $sondage['description'])) ?>
		</div>

		<div class="centre" style="margin-top: 10px;">
			<input type="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>

<h2>Modifier les questions du sondage</h2>

<p class="gras centre"><a href="ajouter-question-<?php echo $sondage['id'] ?>.html">Ajouter une question</a></p>

<?php if (count($questions) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 70%;">Question</th>
			<th style="width: 10%;">Ordre</th>
			<th style="width: 10%;">Modifier</th>
			<th style="width: 10%;">Supprimer</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($questions as $i => $question){ ?>
		<tr class="<?php echo ($i % 2) ? 'odd' : 'even' ?>">
			<td>
				<a href="sondage-<?php echo $sondage['id']; ?>-<?php echo $question['id']; ?>.html">
					<img src="/img/misc/zoom.png" />
				</a>
				<?php echo $view['messages']->parse($question['nom']) ?>

				<?php if (!$question['libre']){ ?>
				<ul class="liste_1">
					<?php foreach ($question->Reponses as $reponse){ ?>
					<li>
						<?php echo htmlspecialchars($reponse['nom']) ?>
						<?php if ($reponse['question_suivante'] == 'suivante'){ ?>
						(<em>&rarr; question suivante</em>)
						<?php } elseif ($reponse['question_suivante'] == 'fin'){ ?>
						(<em>&rarr; sondage terminé</em>)
						<?php } else{ ?>
						(<em>&rarr; question <?php echo $reponse->QuestionSuivante['ordre'] ?></em>)
						<?php } ?>
					</li>
					<?php } ?>
				</ul>
				<?php } else{ ?><br />
				<span style="margin-left: 50px;"><em>Réponse libre</em></span>
				<?php } ?>
			</td>
			<td class="centre">
				<?php if ($i > 0){ ?>
				<a href="?monter=<?php echo $question['id'] ?>" title="Monter la question">
					<img src="/img/misc/monter.png" alt="Monter" />
				</a>
				<?php } ?>

				<?php if ($i + 1 < $sondage['nb_questions']){ ?>
				<a href="?descendre=<?php echo $question['id'] ?>" title="Descendre la question">
					<img src="/img/misc/descendre.png" alt="Descendre" />
				</a>
				<?php } ?>
			</td>
			<td class="centre">
				<a href="modifier-question-<?php echo $question['id'] ?>.html" title="Modifier la question">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</td>
			<td class="centre">
				<a href="supprimer-question-<?php echo $question['id']; ?>.html" title="Supprimer la question">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>

</table>
<?php } ?>


<div class="UI_box gras centre" style="margin-top: 20px;">
	<?php if (verifier('sondages_ajouter')){ ?>
	<a href="ajouter.html">Créer un nouveau sondage</a><br />
	<?php } ?>
	<a href="gestion.html">Retour à la gestion des sondages</a>
</div>

<script type="text/javascript">
window.addEvent('domready', function(){
	$('row_description').slide('hide');
});
</script>
