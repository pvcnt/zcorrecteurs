<?php if (count($questions) > 0){ ?>
<form method="post" action="<?php if (isset($_action)) echo $_action ?>" id="form_jouer">
	<input type="hidden" name="quiz_id" value="<?php echo $quiz['id'] ?>" />
	<?php $i = 0 ?>
	<?php foreach ($questions as $key => $question){ ?>
	<input type="hidden" name="rep[]" value="<?php echo $question['id'] ?>" />

	<fieldset>
		<span class="flot_droite italique">
			<?php if (verifier('quiz_editer_questions') || ($question['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_ses_questions'))){ ?>
			<a href="/quiz/editer-question-<?php echo $question['id']; ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
			<?php } if (verifier('quiz_supprimer_questions') || verifier('quiz_supprimer_ses_questions')){ ?>
			<a href="/quiz/supprimer-question-<?php echo $question['id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
			<?php } ?>
		</span>

		<p class="gras">
			Question <?php echo $key+1 ?> : <?php echo $view['messages']->parse($question['question']); ?>
		</p>

		<div id="correction_<?php echo $question['id'] ?>" class="correction">
		</div>

		<input type="radio" value="1" id="<?php echo 'id'.(++$i); ?>" name="rep<?php echo $question['id']; ?>" />
		<label style="float: none;" for="<?php echo 'id'.$i; ?>" id="q<?php echo $question['id'] ?>r1"> <em>1.</em> <?php echo $view['messages']->parse($question['reponse1']); ?></label><br />

		<input type="radio" value="2" id="<?php echo 'id'.(++$i); ?>" name="rep<?php echo $question['id']; ?>" />
		<label style="float: none;" for="<?php echo 'id'.$i; ?>" id="q<?php echo $question['id'] ?>r2"> <em>2.</em> <?php echo $view['messages']->parse($question['reponse2']); ?></label><br />

		<?php if (!empty($question['reponse3'])){ ?>
		<input type="radio" value="3" id="id<?php echo ++$i; ?>" name="rep<?php echo $question['id']; ?>" />
		<label style="float: none;" for="id<?php echo $i; ?>" id="q<?php echo $question['id'] ?>r3"> <em>3.</em> <?php echo $view['messages']->parse($question['reponse3']); ?></label><br />
		<?php } ?>

		<?php if (!empty($question['reponse4'])){ ?>
		<input type="radio" value="4" id="id<?php echo ++$i; ?>" name="rep<?php echo $question['id']; ?>" />
		<label style="float: none;" for="id<?php echo $i; ?>" id="q<?php echo $question['id'] ?>r4"> <em>4.</em> <?php echo $view['messages']->parse($question['reponse4']); ?></label><br />
		<?php } ?>

		<input type="radio" value="0" id="id<?php echo ++$i; ?>" name="rep<?php echo $question['id']; ?>" checked="checked" />
		<label style="float: none;" for="id<?php echo $i; ?>" id="q<?php echo $question['id'] ?>r0"> <em>Je ne sais pas.</em></label>

		<?php if (isset($_justification)): ?>
		<div class="qz_justification">
			<textarea name="commentaires[<?php echo $question['id'] ?>]"></textarea>
		</div>
		<?php endif ?>
	</fieldset>
	<?php } ?>

	<div class="send">
		<input type="submit" name="submit" value="Envoyer" id="submit" />
	</div>
</form>

<?php if (isset($_justification)): ?>
    <?php $view['javelin']->initBehavior('quiz-comment-answers') ?>
<?php endif ?>

<?php } else{ ?>
<p>Aucune question dans ce quiz, désolé.</p>
<?php } ?>
