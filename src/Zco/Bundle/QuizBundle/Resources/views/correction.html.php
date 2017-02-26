<?php $view->extend('::layouts/default.html.php') ?>

<h1>Correction du quiz « <?php echo htmlspecialchars($InfosQuiz['nom']); ?> »</h1>

<?php if (!empty($InfosQuiz['description'])){ ?>
	<h2><?php echo htmlspecialchars($InfosQuiz['description']); ?></h2>
<?php } ?>

<p>
	Voici maintenant la correction du quiz. Vous pourrez voir la réponse
	donnée, ainsi que la bonne réponse, avec un petit mot d'explication
	quand nous l'avons jugé utile.
</p>

<?php if (!verifier('connecte')){ ?>
<div class="rmq information">
	Vous n'êtes pas connecté, vous ne pourrez donc pas retrouver l'historique
	de vos scores. Pour cela, vous pouvez 
	<a href="<?php echo $view['router']->path('zco_user_session_login') ?>">vous connecter</a>
	ou <a href="<?php echo $view['router']->path('zco_user_session_register') ?>">vous inscrire</a>.
</div>
<?php } ?>

<?php if (count($ListeQuestions) > 0){ ?>
<?php foreach ($ListeQuestions as $question){ ?>
	<?php if (isset($_POST['rep'.$question['id']])){ ?>
	<fieldset>
		<span class="flot_droite italique">
			<?php if (verifier('quiz_editer_questions') || ($q['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_ses_questions'))){ ?>
			<a href="editer-question-<?php echo $question['id']; ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
			<?php } if (verifier('quiz_supprimer_questions') || verifier('quiz_supprimer_ses_questions')){ ?>
			<a href="supprimer-question-<?php echo $question['id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
			<?php } ?>
			</span>

		<p class="gras"><?php echo $view['messages']->parse($question['question']); ?></p>

		<p style="font-size: 1.2em;">
			<?php if ($_POST['rep'.$question['id']] == $question['reponse_juste'] && $_POST['rep'.$question['id']] != 0){ ?>
				<img src="/bundles/zcoquiz/img/juste.png" alt="" /> <span class="vertf">Réponse juste</span>
			<?php } else{ ?>
				<img src="/bundles/zcoquiz/img/faux.png" alt="" /> <span class="rouge">Réponse fausse</span>
			<?php } ?>
		</p>

		<p>
			Vous avez répondu : <em><?php echo $_POST['rep'.$question['id']] != 0 ? $view['messages']->parse($question['reponse'.$_POST['rep'.$question['id']]]) : 'Je ne sais pas.'; ?></em><br />
			La bonne réponse est : <em><?php echo $view['messages']->parse($question['reponse'.$question['reponse_juste']]); ?></em>
		</p>

		<?php if(!empty($question['explication'])){ ?>
			<p>
				<em>Explication : </em><br />
				<?php echo $view['messages']->parse($question['explication']); ?>
			</p>
		<?php } ?>
	</fieldset>
	<?php } ?>
<?php } ?>

<p>Votre note finale est de <strong><?php echo $Note; ?>/20</strong>.</p>

<?php } else{ ?>
<p>Désolé, mais ce quiz ne comportait aucune question.</p>
<?php } ?>

<p class="centre gras">
	Retour <a href="/quiz/quiz-<?php echo $_GET['id']; ?>-<?php echo rewrite($InfosQuiz['nom']); ?>.html">au quiz</a>
	ou <a href="/quiz/">à la liste des quiz</a>.
</p>
