<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Gérer les quiz</h1>

<p>Vous pouvez voir tous les quiz classés par catégorie et les éditer / supprimer.</p>

<p class="bold center"><a href="ajouter-quiz.html">Ajouter un nouveau quiz</a></p>

<?php if($ListerQuiz){ $colspan = 5; ?>
<table class="table table-striped">
	<thead>
		<tr>
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
				echo '<tr><td colspan="'.$colspan.'" class="bold">'.htmlspecialchars($quiz->Categorie['nom']).'</td></tr>';
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
				<?php if ($quiz['aleatoire'] >= 2){ ?>
					<em>(<?php echo $quiz['aleatoire'] ?> aléatoires)</em>
				<?php } ?>
			</td>
			<td class="centre">
				<a href="ajouter-question-<?php echo $quiz['id']; ?>.html"><img src="/bundles/zcoquiz/img/ajouter.png" alt="Ajouter" /></a>
				<a href="editer-quiz-<?php echo $quiz['id']; ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
				<a href="supprimer-quiz-<?php echo $quiz['id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
                <a href="valider-quiz-<?php echo $quiz->id ?>-<?php echo (int)(!$quiz->visible) ?>.html">
                    <?php if ($quiz->visible): ?>
                    <img src="/pix.gif" class="fff forbidden" alt="Masquer" title="Masquer"/>
                    <?php else: ?>
                    <img src="/pix.gif" class="fff tick" alt="Valider" title="Valider"/>
                    <?php endif ?>
                </a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun quiz n'a encore été créé.</p>
<?php } ?>
