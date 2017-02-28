<?php if (count($questions) > 0){ ?>
<form method="post" action="<?php if (isset($_action)) echo $_action ?>" id="form_jouer" class="form-horizontal">
	<input type="hidden" name="quiz_id" value="<?php echo $quiz['id'] ?>" />
	<?php $i = 0 ?>
	<?php foreach ($questions as $key => $question){ ?>
	<input type="hidden" name="rep[]" value="<?php echo $question['id'] ?>" />

		<p class="bold">
			Question <?php echo $key+1 ?> : <?php echo $view['messages']->parse($question['question']); ?>
		</p>
		<div id="correction_<?php echo $question['id'] ?>" class="correction"></div>

        <?php for ($j = 1; $j <= 4; $j++): ?>
        <label class="radio" style="float: none;" for="<?php echo 'id'.$i; ?>" id="q<?php echo $question['id'] ?>r<?php echo $j ?>">
		    <input type="radio" value="<?php echo $j ?>" id="<?php echo 'id'.(++$i); ?>" name="rep<?php echo $question['id']; ?>" />
		    <em><?php echo $j ?>.</em> <?php echo $view['messages']->parse($question['reponse' . $j]); ?><br />
        </label>
        <?php endfor ?>

        <label class="radio" style="float: none;" for="id<?php echo $i; ?>" id="q<?php echo $question['id'] ?>r0">
            <input type="radio" value="0" id="id<?php echo ++$i; ?>" name="rep<?php echo $question['id']; ?>" checked="checked" />
            <em>Je ne sais pas.</em>
        </label>

		<?php if (isset($_justification)): ?>
		<div class="qz_justification">
			<textarea name="commentaires[<?php echo $question['id'] ?>]"></textarea>
		</div>
		<?php endif ?>
	<?php } ?>

	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="Envoyer" id="submit" />
	</div>
</form>

<?php if (isset($_justification)): ?>
    <?php $view['javelin']->initBehavior('quiz-comment-answers') ?>
<?php endif ?>

<?php } else{ ?>
<p>Aucune question dans ce quiz, désolé.</p>
<?php } ?>
