<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier une question</h1>
<h2><?php echo htmlspecialchars($sondage['nom']) ?></h2>

<form method="post" action="">
	<fieldset>
		<legend>Informations sur la question</legend>
		<label for="nom">Question : </label>
		<?php echo $view->render('::zform.html.php', array('texte' => $question['nom'])) ?><br />

		<label for="libre">Question à réponse libre :</label>
		<input type="checkbox" name="libre" id="libre"<?php if ($question['libre']) echo ' checked="checked"' ?> /><br />

		<label for="obligatoire">Vote blanc possible :</label>
		<input type="checkbox" name="obligatoire" id="obligatoire"<?php if (!$question['obligatoire']) echo ' checked="checked"' ?> /><br />

		<label for="resultats_publics">Résultats affichés publiquement :</label>
		<input type="checkbox" name="resultats_publics" id="resultats_publics"<?php if ($question['resultats_publics']) echo ' checked="checked"' ?> /><br />

		<div id="row_libre">
			<label for="nb_min_choix">Nombre minimum de choix :</label>
			<select name="nb_min_choix" id="nb_min_choix">
				<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
				<option value="<?php echo $i ?>"<?php if ($question['nb_min_choix'] == $i) echo ' selected="selected"' ?>>
					<?php echo $i ?>
				</option>
				<?php } ?>
			</select><br />

			<label for="nb_max_choix">Nombre maximum de choix :</label>
			<select name="nb_max_choix" id="nb_max_choix">
				<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
				<option value="<?php echo $i ?>"<?php if ($question['nb_max_choix'] == $i) echo ' selected="selected"' ?>>
					<?php echo $i ?>
				</option>
				<?php } ?>
			</select>
		</div><br />

		<label for="raz_votes" class="rouge gras">Effacer tous les votes de cette question :</label>
		<input type="checkbox" name="raz_votes" id="raz_votes" />
	</fieldset>

	<fieldset id="row_libre2">
		<legend>Intitulés des réponses</legend>
		<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
		<label for="reponse_<?php echo $i ?>">Réponse n<sup>o</sup>&nbsp;<?php echo $i ?> :</label>
		<input type="text" name="reponse_<?php echo $i ?>" id="reponse_<?php echo $i ?>" size="40" value="<?php echo isset($reponses[$i]) ? htmlspecialchars($reponses[$i]['nom']) : '' ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<label for="question_suivante_<?php echo $i ?>" class="nofloat">Après la réponse, aller à :</label>
		<select name="question_suivante_<?php echo $i ?>" id="question_suivante_<?php echo $i ?>">
			<option value="suivante"<?php if (isset($reponses[$i]) && $reponses[$i]['question_suivante'] == 'suivante') echo ' selected="selected"' ?>>
				Question suivante
			</option>

			<?php foreach ($questions as $j => $quest){ ?>
			<option value="<?php echo $quest['id'] ?>"<?php if (isset($reponses[$i]) && $reponses[$i]['question_suivante'] == 'id' && $reponses[$i]['question_suivante_id'] == $quest['id']) echo ' selected="selected"' ?>>
				<?php echo ($j+1).') '.htmlspecialchars(strip_tags($quest['nom'])) ?>
			</option>
			<?php } ?>
			<option value="fin"<?php if (isset($reponses[$i]) && $reponses[$i]['question_suivante'] == 'fin') echo ' selected="selected"' ?>>
				Sondage terminé
			</option>
		</select>
		<br />
		<?php } ?>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>

<div class="UI_box gras centre">
	<a href="ajouter-question-<?php echo $question['sondage_id'] ?>.html">Ajouter une nouvelle question</a><br />
	<a href="modifier-<?php echo $question['sondage_id'] ?>.html">Retour à la modification du sondage</a>
</div>

<script type="text/javascript">
$('libre').addEvent('change', function(){
	if (this.checked)
		$$('#row_libre, #row_libre2').setStyle('display', 'none');
	else
		$$('#row_libre, #row_libre2').setStyle('display', 'block');
});

<?php if ($question['libre']){ ?>
window.addEvent('domready', function(){
	$$('#row_libre, #row_libre2').setStyle('display', 'none');
});
<?php } ?>
</script>