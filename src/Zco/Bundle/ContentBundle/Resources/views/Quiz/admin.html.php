<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div class="float-right">
    <a href="<?php echo $view['router']->path('zco_quiz_newQuiz') ?>" class="btn btn-primary">
        <i class="icon-plus-sign icon-white"></i>
        Ajouter un quiz
    </a>
</div>


<h1>Gérer les quiz</h1>

<?php if($quizList){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Création</th>
			<th>Actions</th>
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
				<a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $quiz['id']]) ?>">
				    <?php echo $quiz['nom']; ?>
				</a>
			</td>
			<td>
				<?php echo dateformat($quiz['date']); ?> par <?php echo $quiz->Utilisateur ?>
			</td>
			<td class="center">
                <a href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $quiz['id']]) ?>"
                    ><img title="Modifier" alt="Modifier" class="fff pencil" src="/pix.gif"/></a>
                <a href="<?php echo $view['router']->path('zco_quiz_deleteQuiz', ['id' => $quiz['id']]) ?>"
                    ><img title="Supprimer" alt="Supprimer" class="fff cross" src="/pix.gif"/></a>
                <?php if ($quiz['visible']): ?>
                    <a href="<?php echo $view['router']->path('zco_quiz_publish', ['id' => $quiz['id'], 'status' => 0, 'token' => $_SESSION['token']]) ?>" title="Masquer"
                    ><img alt="Masquer" class="fff forbidden" src="/pix.gif"/></a>
                <?php else: ?>
                    <a href="<?php echo $view['router']->path('zco_quiz_publish', ['id' => $quiz['id'], 'status' => 1, 'token' => $_SESSION['token']]) ?>" title="Publier"
                    ><img alt="Publier" class="fff tick" src="/pix.gif"/></a>
                <?php endif; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucun quiz n'a encore été créé.</p>
<?php } ?>
