<?php $view->extend('::layouts/bootstrap.html.php') ?>

<span style="float: right;">
	<?php if (verifier('quiz_stats_generales')){ ?>
	<a href="statistiques-<?php echo $quiz['id'] ?>.html">
		<img src="/img/membres/stats_zco.png" alt="Statistiques" />
		Statistiques
	</a>
	<?php } if (verifier('quiz_ajouter')){ ?>
	<a href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $quiz['id']]) ?>" title="Modifier le quiz">
		<img src="/img/editer.png" alt="Modifier le quiz" />
	</a>
        <a href="<?php echo $view['router']->path('zco_quiz_deleteQuiz', ['id' => $quiz['id']]) ?>" title="Supprimer le quiz">
		<img src="/img/supprimer.png" alt="Supprimer le quiz" />
	</a>
	<?php } ?>
</span>

<h1>
    <?php echo htmlspecialchars($quiz['nom']); ?>
    <?php if (!empty($quiz['description'])){ ?>
        <small><?php echo htmlspecialchars($quiz['description']); ?></small>
    <?php } ?>
</h1>

<div class="alert alert-heading">
	<strong>Règles :</strong> sélectionnez la bonne réponse. Il n'y a qu'une
	seule réponse juste pour chaque question. Une réponse fausse, de même que
	l'absence de réponse n'enlève aucun point.
</div><br />

<div id="quiz_note" class="alert alert-info" style="display: none;"></div>

<?php if (count($questions) > 0){ ?>
    <form method="post" id="form_jouer" class="form-horizontal">
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
                    <input type="radio" value="<?php echo $j ?>" id="<?php echo 'id'.($i++); ?>" name="rep<?php echo $question['id']; ?>" />
                    <em><?php echo $j ?>.</em> <?php echo $view['messages']->parse($question['reponse' . $j]); ?><br />
                </label>
            <?php endfor ?>

            <label class="radio" style="float: none;" for="id<?php echo $i; ?>" id="q<?php echo $question['id'] ?>r0">
                <input type="radio" value="0" id="id<?php echo ++$i; ?>" name="rep<?php echo $question['id']; ?>" checked="checked" />
                <em>Je ne sais pas.</em>
            </label>
        <?php } ?>

        <div class="form-actions">
            <input type="submit" name="submit" class="btn btn-primary" value="Envoyer" id="submit" />
        </div>
    </form>

<?php } else{ ?>
    <p>Aucune question dans ce quiz, désolé.</p>
<?php } ?>
