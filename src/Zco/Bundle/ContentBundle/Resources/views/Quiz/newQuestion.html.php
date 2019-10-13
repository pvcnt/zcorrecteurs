<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Ajouter une question au quiz</h1>

<form method="post" action="" class="form-horizontal">
	<fieldset>
		<legend>Question</legend>
        <div class="control-group">
            <label for="question" class="control-label">Question</label>
            <div class="controls">
                <?php echo $view->render('::zform.html.php', array('id' => 'question')) ?>
            </div>
        </div>
	</fieldset>
	<fieldset>
		<legend>Réponses</legend>
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="control-group">
            <label for="rep<?php echo $i ?>" class="control-label">Réponse <?php echo $i ?></label>
            <div class="controls">
                <input type="text" name="rep<?php echo $i ?>" id="rep<?php echo $i ?>" class="input-xxlarge" />
            </div>
        </div>
        <?php endfor ?>
        <div class="control-group">
		    <label for="rep_juste" class="control-label">Réponse juste : </label>
            <div class="controls">
                <select name="rep_juste" id="rep_juste">
                    <option value="1">Réponse 1</option>
                    <option value="2">Réponse 2</option>
                    <option value="3">Réponse 3</option>
                    <option value="4">Réponse 4</option>
                </select>
            </div>
        </div>
	</fieldset>
	<fieldset>
		<legend>Explication</legend>
        <div class="control-group">
            <label for="texte" class="control-label">Explication</label>
            <div class="controls">
                <?php echo $view->render('::zform.html.php', array('id' => 'texte')); ?>
            </div>
        </div>
	</fieldset>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
		<a class="btn" href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $quiz['id']]) ?>">Annuler</a>
	</div>
</form>
