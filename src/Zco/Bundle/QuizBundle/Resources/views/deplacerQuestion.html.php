<?php $view->extend('::layouts/default.html.php') ?>

<h1>Déplacer une quesion</h1>

<p>Cette page vous permet de déplacer une question d'un quiz à un autre.<br/>
La question suivante sera supprimée du quiz
« <?php echo htmlspecialchars($ancienQuiz->nom) ?> »
et insérée dans le quiz sélectionné.</p>

<div class="rmq question"><?php echo $view['messages']->parse($question['question']) ?></div>
<ul>
	<?php for ($i = 1 ; $i <= 4 ; $i++){ ?>
	<?php if (!empty($question['reponse'.$i])){ ?>
	<li<?php if ($question['reponse_juste'] == $i) echo ' class="gras vertf"' ?>>
		<?php echo $view['messages']->parse($question['reponse'.$i]) ?>
	</li>
	<?php } } ?>
</ul>

<form action="" method="post">
	<fieldset>
		<legend>Déplacer une question</legend>

		<p>
			<label for="input_quiz">Nouveau Quiz :</label>
			<select name="quiz" id="input_quiz">
				<option value="" class="italique">&nbsp;-- Sélectionnez un quiz</option>

				<?php foreach($listeQuiz as $q): ?>
					<?php if($q['id'] != $ancienQuiz->id): ?>
					<option value="<?php echo $q['id'] ?>">
						<?php echo htmlspecialchars($q['nom']) ?>
					</option>
					<?php endif ?>
				<?php endforeach ?>
			</select>
		</p>
		<p>
			<input type="submit" value="Déplacer"/>
		</p>
	</fieldset>
</form>
