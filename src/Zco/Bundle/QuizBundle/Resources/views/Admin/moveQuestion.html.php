<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Déplacer une question</h1>

<p>
    Cette page vous permet de déplacer une question d'un quiz à un autre.<br/>
    La question suivante sera supprimée du quiz «&nbsp;<?php echo htmlspecialchars($oldQuiz->nom) ?>&nbsp;»
    et insérée dans le quiz sélectionné.</p>

<div class="alert alert-info"><?php echo $view['messages']->parse($question['question']) ?></div>

<ul>
    <?php for ($i = 1; $i <= 4; $i++) { ?>
        <?php if (!empty($question['reponse' . $i])) { ?>
            <li<?php if ($question['reponse_juste'] == $i) echo ' class="gras vertf"' ?>>
                <?php echo $view['messages']->parse($question['reponse' . $i]) ?>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

<form action="" method="post" class="form-horizontal">
    <div class="control-group">
        <label for="input_quiz" class="control-label">Nouveau quiz</label>
        <div class="controls">
            <select name="quiz" id="input_quiz">
                <option value="" class="italic">&nbsp;-- Sélectionnez un quiz</option>
                <?php foreach ($quizList as $q): ?>
                    <?php if ($q['id'] != $oldQuiz->id): ?>
                        <option value="<?php echo $q['id'] ?>">
                            <?php echo htmlspecialchars($q['nom']) ?>
                        </option>
                    <?php endif ?>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Déplacer"/>
        <a class="btn" href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $question['quiz_id']]) ?>">Annuler</a>
    </div>
</form>
