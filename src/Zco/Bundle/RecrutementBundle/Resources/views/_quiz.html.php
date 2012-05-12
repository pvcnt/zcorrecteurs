<?php $num = 1; foreach ($questions as $question): ?>
<fieldset>
	<p class="gras">Question <?php echo $num++ ?> : <?php echo htmlspecialchars($question['question']) ?></p>

	<div class="correction">
		<?php if ($question['reponse_juste'] == $question['reponse_id']): ?>
			<div class="correction juste"><span class="type">Bonne réponse</span><br />
		<?php else: ?>
			<div class="correction faux"><span class="type">Mauvaise réponse</span><br />
		<?php endif;

		if(!empty($question['justification'])): ?>
		<p>Le candidat a justifié sa réponse.</p>
		<div id="explication_<?php echo $question['question_id'] ?>" class="explication">
			<?php echo $view['messages']->parse($question['justification']) ?>
		</div>
		<?php endif ?>
	</div>

	<p>
		<?php for ($i = 1; $i <= 4; $i++):
		if (empty($question['reponse'.$i])) continue; ?>
			<input type="radio"
				   value="<?php echo $i ?>"
				   id="q<?php echo $question['question_id'].'r'.$i ?>"
				   name="rep<?php echo $question['question_id'] ?>"
				   <?php if ($question['reponse_id'] == $i) echo ' checked="checked"';
				         else echo ' disabled="disabled"' ?>/>
			<label style="float: none;<?php
		           if ($question['reponse_juste'] == $i) echo 'color:green';
		           elseif ($question['reponse_id'] == $i) echo 'color:red'; ?>"
		           for="q<?php echo $question['question_id'].'r'.$i ?>">
				<em><?php echo $i ?>.</em> <?php echo htmlspecialchars($question['reponse'.$i]) ?>
			</label>
			<br/>
		<?php endfor ?>

		<input type="radio"
			   value="0"
			   id="q<?php echo $question['question_id'].'r' ?>5"
			   name="rep<?php echo $question['question_id'] ?>"
		       <?php if ($question['reponse_id'] == 0) echo ' checked="checked"';
		             else echo ' disabled="disabled"' ?>/>
		<label style="float: none;<?php
		              if ($question['reponse_id'] == 0) echo 'color:red'; ?>"
		       for="q<?php echo $question['question_id'].'r' ?>5">
			<em>Je ne sais pas.</em>
		</label>
	</p>
	</div>
</fieldset>
<?php endforeach ?>
