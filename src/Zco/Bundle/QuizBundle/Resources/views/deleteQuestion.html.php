<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer une question</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer cette question intitulée
        <strong><?php echo htmlspecialchars($question['question']); ?></strong> ?
    </p>
    <div class="form-actions">
        <input type="submit" value="Oui" class="btn btn-primary"/>
        <a class="btn" href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $question['quiz_id']]) ?>">Non</a>
    </div>
</form>