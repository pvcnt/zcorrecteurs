<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Gérer les quiz</h1>

<p>Vous pouvez voir tous les quiz classés par catégorie et les éditer / supprimer.</p>

<p class="bold center">
    <a href="<?php echo $view['router']->path('zco_quiz_newQuiz') ?>">Ajouter un nouveau quiz</a>
</p>

<?php if($quizList){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Création</th>
			<th>Difficulté</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$current = null;
		foreach($quizList as $quiz)
		{
			if($current != $quiz->Categorie['id'])
			{
				$current = $quiz->Categorie['id'];
				echo '<tr><td colspan="5" class="bold">'.htmlspecialchars($quiz->Categorie['nom']).'</td></tr>';
			}
		?>
		<tr>
			<td>
				<?php if($quiz->visible): ?>
				<a href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $quiz['id']]) ?>">
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
                <?php echo str_repeat(
                    '<img src="/bundles/zcoquiz/img/etoile.png" alt="' . $quiz['difficulte'] . '" title="' . $quiz['difficulte'] . '" />',
                    $quiz->getNumericLevel()
                ) ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun quiz n'a encore été créé.</p>
<?php } ?>
