<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gérer les quiz</h1>

<p>Vous pouvez voir tous les quiz classés par catégorie et les éditer / supprimer.</p>

<?php if(verifier('quiz_ajouter')){ ?>
<p class="gras centre"><a href="ajouter-quiz.html">Ajouter un nouveau quiz</a></p>
<?php } ?>

<?php if($ListerQuiz){ $colspan = 5; ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th>Nom</th>
			<th>Création</th>
			<th>Difficulté</th>
			<th>Questions</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$current = null;

		foreach($ListerQuiz as $quiz)
		{
			if($current != $quiz->Categorie['id'])
			{
				$current = $quiz->Categorie['id'];
				echo '<tr><td colspan="'.$colspan.'" class="gras">'.htmlspecialchars($quiz->Categorie['nom']).'</td></tr>';
			}
		?>
		<tr>
			<td>
				<?php if($quiz->visible): ?>
				<a href="/quiz/quiz-<?php echo $quiz['id']; ?>.html">
				<?php endif ?>

				<?php echo $quiz['nom']; ?>

				<?php if($quiz->visible): ?>
				</a>
				<?php endif ?>
			</td>
			<td>
				<?php echo dateformat($quiz['date']); ?> par <?php echo $quiz->Utilisateur ?>
			</td>
			<td>
				<?php echo $quiz->afficherEtoiles(); ?>
			</td>
			<td class="centre">
				<?php echo $quiz['nb_questions']; ?>
				<?php if ($quiz['aleatoire'] == true){ ?>
					<em>(aléatoire)</em>
				<?php } ?>
			</td>
			<td class="centre">
				<?php if(verifier('quiz_ajouter_questions') || ($q['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_ajouter_questions_siens'))){ ?>
				<a href="ajouter-question-<?php echo $quiz['id']; ?>.html">
					<img src="/img/quiz/ajouter.png" alt="Ajouter" />
				</a>
				<?php }
				if(verifier('quiz_ajouter_questions') || verifier('quiz_editer_questions')
					|| verifier('quiz_supprimer_questions') || verifier('quiz_ajouter_questions_siens')
					|| verifier('quiz_editer_ses_questions') || verifier('quiz_supprimer_ses_questions')
					|| verifier('quiz_editer') || verifier('quiz_editer_siens')){ ?>

				<a href="editer-quiz-<?php echo $quiz['id']; ?>.html">
					<img src="/img/editer.png" alt="Modifier" />
				</a>

				<?php } ?>
				<?php if(verifier('quiz_supprimer') || ($q['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_supprimer_siens'))){ ?>
				<a href="supprimer-quiz-<?php echo $quiz['id']; ?>.html">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
				<?php } ?>
				<?php if (verifier('quiz_ajouter')): ?>
					<a href="valider-quiz-<?php echo $quiz->id ?>-<?php
						echo (int)(!$quiz->visible) ?>.html">
					<?php if ($quiz->visible): ?>
					<img src="/pix.gif" class="fff forbidden" alt="Masquer" title="Masquer"/>
					<?php else: ?>
					<img src="/pix.gif" class="fff tick" alt="Valider" title="Valider"/>
					<?php endif ?>
				<?php endif ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun quiz n'a encore été créé.</p>
<?php } ?>
