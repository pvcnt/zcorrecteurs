<?php $view->extend('::layouts/default.html.php') ?>

<h1>Postuler à un recrutement</h1>

<fieldset>
	<legend>Répondre au questionnaire à choix multiples</legend>
	<?php if (!empty($InfosCandidature['candidature_quiz_score'])): ?>
		<p>Vous avez déjà répondu au questionnaire.</p>
	<?php else: ?>
		<p>
			<strong>Règles : </strong>sélectionnez la bonne réponse. Il n'y a qu'une
			seule réponse juste pour chaque question. Une réponse fausse, de même que
			l'absence de réponse n'enlève aucun point.
		</p>
		<p class="gras rouge">La validation du questionnaire ne peut se faire qu'une fois et est définitive !</p>
		<?php echo $view->render('ZcoQuizBundle::_jouer.html.php', array('questions' => $quiz->Questions(), 'quiz' => $quiz,
		                                              '_justification' => true,
		                                              '_action' => 'postuler-'.$_GET['id']
		                                              .'-'.rewrite($InfosRecrutement['recrutement_nom']).'.html')) ?>
	<?php endif; ?>
</fieldset>
