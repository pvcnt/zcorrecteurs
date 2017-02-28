<?php $view->extend('::layouts/bootstrap.html.php') ?>

<span style="float: right;">
	<?php if (verifier('quiz_stats_generales')){ ?>
	<a href="statistiques-<?php echo $InfosQuiz['id'] ?>.html">
		<img src="/img/membres/stats_zco.png" alt="Statistiques" />
		Statistiques
	</a>
	<?php } if (verifier('quiz_ajouter')){ ?>
	<a href="editer-quiz-<?php echo $InfosQuiz['id'] ?>.html" title="Modifier le quiz">
		<img src="/img/editer.png" alt="Modifier le quiz" />
	</a>
	<a href="supprimer-quiz-<?php echo $InfosQuiz['id'] ?>.html" title="Supprimer le quiz">
		<img src="/img/supprimer.png" alt="Supprimer le quiz" />
	</a>
	<?php } ?>
</span>

<h1>
    <?php echo htmlspecialchars($InfosQuiz['nom']); ?>
    <?php if (!empty($InfosQuiz['description'])){ ?>
        <small><?php echo htmlspecialchars($InfosQuiz['description']); ?></small>
    <?php } ?>
</h1>

<div class="alert alert-heading">
	<strong>Règles :</strong> sélectionnez la bonne réponse. Il n'y a qu'une
	seule réponse juste pour chaque question. Une réponse fausse, de même que
	l'absence de réponse n'enlève aucun point.
</div><br />

<div class="alert alert-error" id="quiz_notice" style="display: none;"></div>
<div id="quiz_note" class="alert alert-info" style="display: none;"></div>

<?php echo $view->render('ZcoQuizBundle::_jouer.html.php', array('questions' => $ListeQuestions, 'quiz' => $InfosQuiz)) ?>