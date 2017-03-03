<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($question->Quiz['nom']) ?></h1>

<form method="post" action="<?php echo $view['router']->path('zco_quiz_editQuestion', ['id' => $question['id']]) ?>" class="form-horizontal">
    <fieldset>
        <legend>Question</legend>
        <div class="control-group">
            <label for="question" class="control-label">Question</label>
            <div class="controls">
                <?php echo $view->render('::zform.html.php', array('id' => 'question', 'texte' => $question['question'])) ?>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Réponses</legend>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="control-group">
                <label for="rep<?php echo $i ?>" class="control-label">Réponse <?php echo $i ?></label>
                <div class="controls">
                    <input type="text" name="rep<?php echo $i ?>" id="rep<?php echo $i ?>"
                           value="<?php echo htmlspecialchars($question['reponse' . $i]); ?>" class="input-xxlarge"/>
                </div>
            </div>
        <?php endfor ?>
        <div class="control-group">
            <label for="rep_juste" class="control-label">Réponse juste</label>
            <div class="controls">
                <select name="rep_juste" id="rep_juste">
                    <?php for ($i = 1; $i <= 4; $i++) { ?>
                        <option value="<?php echo $i ?>"<?php if ($question['reponse_juste'] == $i) echo ' selected="selected"'; ?>>
                            Réponse <?php echo $i ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Explication</legend>
        <div class="control-group">
            <label for="texte" class="control-label">Explication</label>
            <div class="controls">
                <?php echo $view->render('::zform.html.php', array('id' => 'texte', 'texte' => $question['explication'])) ?>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Envoyer"/>
        <a href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $question['quiz_id']]) ?>" class="btn">Annuler</a>
    </div>
</form>
