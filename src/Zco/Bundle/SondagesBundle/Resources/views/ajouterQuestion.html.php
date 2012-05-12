<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter une question</h1>
<h2><?php echo htmlspecialchars($sondage['nom']) ?></h2>

<form method="post" action="">
	<fieldset>
		<legend>Informations sur la question</legend>
		<label for="nom">Question : </label>
		<?php echo $view->render('::zform.html.php'); ?><br />

		<label for="libre">Question à réponse libre :</label>
		<input type="checkbox" name="libre" id="libre" /><br />

		<label for="obligatoire">Vote blanc possible :</label>
		<input type="checkbox" name="obligatoire" id="obligatoire" checked="checked" /><br />

		<label for="resultats_publics">Résultats affichés publiquement :</label>
		<input type="checkbox" name="resultats_publics" id="resultats_publics" checked="checked" /><br />

		<div id="row_libre">
			<label for="nb_min_choix">Nombre minimum de choix :</label>
			<select name="nb_min_choix" id="nb_min_choix">
				<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
				<?php } ?>
			</select><br />

			<label for="nb_max_choix">Nombre maximum de choix :</label>
			<select name="nb_max_choix" id="nb_max_choix">
				<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
				<?php } ?>
			</select>
		</div>
	</fieldset>

	<fieldset id="row_libre2">
		<legend>Intitulés des réponses</legend>
		<?php for ($i = 1 ; $i <= 10 ; $i++){ ?>
		<label for="reponse_<?php echo $i ?>">Réponse n<sup>o</sup>&nbsp;<?php echo $i ?> :</label>
		<input type="text" name="reponse_<?php echo $i ?>" id="reponse_<?php echo $i ?>" size="40" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<label for="question_suivante_<?php echo $i ?>" class="nofloat">Après la réponse, aller à :</label>
		<select name="question_suivante_<?php echo $i ?>" id="question_suivante_<?php echo $i ?>">
			<option value="suivante">Question suivante</option>
			<?php foreach ($questions as $question){ ?>
			<option value="<?php echo $question['id'] ?>">
				<?php echo htmlspecialchars(strip_tags($question['nom'])) ?>
			</option>
			<?php } ?>
			<option value="fin">Sondage terminé</option>
		</select>
		<br />
		<?php } ?>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>

<script type="text/javascript">
$('libre').addEvent('change', function(){
	if (this.checked)
		$$('#row_libre, #row_libre2').setStyle('display', 'none');
	else
		$$('#row_libre, #row_libre2').setStyle('display', 'block');
});
</script>