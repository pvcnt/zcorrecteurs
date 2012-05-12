<?php $view->extend('::layouts/default.html.php') ?>

<h1>Quiz sur la langue française et la culture générale</h1>

<img src="/img/quiz/img_accueil.png" alt="" style="float: right;" />

<p>
	Voici la liste de tous les quiz du site, classés par catégorie. Ils vous
	permettent de tester vos connaissances dans divers domaines liés à la langue
	française ou encore la culture générale. Notez que si vous êtes inscrit et
	connecté, vous disposez d'un suivi complet de vos notes ainsi que de graphiques
	pour suivre votre progression !
</p>

<div class="rmq information">
	À noter que si vous souhaitez proposer vos propres questions afin d'enrichir
	ces quiz, vous pouvez nous les soumettre dans
	<a href="/forum/sujet-871-quiz-proposez-vos-questions.html">ce sujet</a>
	réservé à cet usage.
</div>

<?php if (verifier('quiz_ses_stats')){ ?>
<p class="gras centre"><a href="mes-statistiques.html">Accéder à mes statistiques personnelles</a></p>
<?php } ?>

<?php if ($bloc_accueil == 'quiz' && !is_null($QuizSemaine)){ ?>
<div class="UI_box" style="width: 50%;">
	<?php if (!empty($QuizSemaine['image'])){ ?>
	<a href="/quiz/quiz-<?php echo $QuizSemaine['id']; ?>-<?php echo rewrite($QuizSemaine['nom']); ?>.html">
		<img class="flot_droite" src="<?php echo htmlspecialchars($QuizSemaine['image']); ?>" alt="" />
	</a>
	<?php } ?>

	Le quiz suivant de la catégorie « <?php echo htmlspecialchars($QuizSemaine['Categorie']['nom']); ?> »
	est actuellement mis en valeur par l'équipe du site :<br /><br />

	<div>
		<strong><a href="/quiz/quiz-<?php echo $QuizSemaine['id']; ?>-<?php echo rewrite($QuizSemaine['nom']); ?>.html">
			<?php echo htmlspecialchars($QuizSemaine['nom']); ?>
		</a></strong>
		<?php if(!empty($QuizSemaine['description'])){ ?><br />
		<?php echo htmlspecialchars($QuizSemaine['description']); ?>
		<?php } ?>
	</div>
	<div style="clear: right;"></div>
</div>
<?php } ?>

<div style="clear: right;"></div>

<?php
$current = null;
if (count($ListerQuiz) > 0):
	foreach ($ListerQuiz as $key => $quiz):
		if ($quiz['categorie_id'] != $current):
			$current = $quiz['categorie_id'];
			if($key != 0)
			{
				echo '</tbody></table><br />';
			}
			?>
<h2 id="c<?php echo $quiz->Categorie['id'] ?>"><?php echo htmlspecialchars($quiz->Categorie['nom']) ?></h2>

<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 55%;">Nom</th>
			<th style="width: 20%;">Création</th>
			<th style="width: 15%;">Difficulté</th>
			<th style="width: 10%;">Questions</th>
		</tr>
	</thead>
	<tbody>

	<?php endif; ?>

		<tr>
			<td>
				<a href="quiz-<?php echo $quiz['id']; ?>-<?php echo rewrite($quiz['nom']); ?>.html">
					<?php echo htmlspecialchars($quiz['nom']); ?>
				</a>

				<?php if (!empty($quiz['quiz_description'])): ?><br />
				<em><?php echo htmlspecialchars($quiz['description']) ?></em>
				<?php endif; ?>
			</td>
			<td><?php echo dateformat($quiz['date']) ?></td>
			<td>
				<span style="float: right; margin-right: 5px;"><?php echo $quiz['difficulte'] ?></span>
				<?php echo $quiz->afficherEtoiles() ?>
			</td>
			<td class="centre">
				<?php echo $quiz['nb_questions'] ?>
				<?php if ($quiz['aleatoire'] == true): ?>
					<em>(aléatoire)</em>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>Aucun quiz n'est visible.</p>
<?php endif; ?>
