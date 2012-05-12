<?php $view->extend('::layouts/default.html.php') ?>

<span style="float: right;">
	<?php if (verifier('quiz_stats_generales')){ ?>
	<a href="statistiques-<?php echo $InfosQuiz['id'] ?>.html">
		<img src="/img/membres/stats_zco.png" alt="Statistiques" />
		Statistiques
	</a>
	<?php } if (verifier('quiz_editer') ||
		($InfosQuiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_siens')) ||
		verifier('quiz_ajouter_questions') ||
		($InfosQuiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_ajouter_questions_siens')) ||
		verifier('quiz_editer_questions') || verifier('quiz_supprimer_questions') ||
		verifier('quiz_editer_ses_questions') || verifier('quiz_supprimer_ses_questions')
	){ ?>
	<a href="editer-quiz-<?php echo $InfosQuiz['id'] ?>.html" title="Modifier le quiz">
		<img src="/img/editer.png" alt="Modifier le quiz" />
	</a>
	<?php } if (verifier('quiz_supprimer') ||
		($InfosQuiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_supprimer_siens'))
	){ ?>
	<a href="supprimer-quiz-<?php echo $InfosQuiz['id'] ?>.html" title="Supprimer le quiz">
		<img src="/img/supprimer.png" alt="Supprimer le quiz" />
	</a>
	<?php } ?>
</span>

<h1>Participer au quiz « <?php echo htmlspecialchars($InfosQuiz['nom']); ?> »</h1>

<?php if (!empty($InfosQuiz['description'])){ ?>
	<h2><?php echo htmlspecialchars($InfosQuiz['description']); ?></h2>
<?php } ?>

<div class="UI_box">
	<strong>Règles : </strong>sélectionnez la bonne réponse. Il n'y a qu'une
	seule réponse juste pour chaque question. Une réponse fausse, de même que
	l'absence de réponse n'enlève aucun point.
</div><br />

<div class="UI_errorbox" id="quiz_notice" style="display: none;"></div>
<div id="quiz_note" class="UI_infobox" style="display: none;"></div>

<?php echo $view->render('ZcoQuizBundle::_jouer.html.php', array('questions' => $ListeQuestions, 'quiz' => $InfosQuiz)) ?>