<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($quiz['nom']) ?></h1>

<form method="post" action="" class="form-horizontal">
    <div class="control-group">
		<label for="nom" class="control-label">Nom</label>
        <div class="controls">
		    <input type="text" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($quiz['nom']); ?>" />
        </div>
    </div>
    <div class="control-group">
		<label for="description" class="control-label">Description</label>
        <div class="controls">
		    <input type="text" name="description" id="description" class="input-xxlarge" value="<?php echo htmlspecialchars($quiz['description']); ?>" />
        </div>
    </div>
    <div class="control-group">
		<label for="difficulte" class="control-label">Difficulté</label>
        <div class="controls">
            <select name="difficulte" id="difficulte">
                <?php foreach ($levels as $cle => $valeur){ ?>
                <option value="<?php echo $cle; ?>"<?php if($quiz['difficulte'] == $valeur) echo ' selected="selected"'; ?>>
                    <?php echo htmlspecialchars($valeur); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="control-group">
		<label for="categorie" class="control-label">Catégorie</label>
        <div class="controls">
            <select name="categorie" id="categorie">
                <?php foreach ($categories as $categorie){ ?>
                <option value="<?php echo $categorie['cat_id']; ?>"<?php if($quiz['categorie_id'] == $categorie['cat_id']) echo ' selected="selected"'; ?>>
                    <?php echo htmlspecialchars($categorie['cat_nom']); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="control-group">
		<label for="aleatoire" class="control-label">Nombre de réponses choisies dans un ordre aléatoire</label>
        <div class="controls">
            <input type="number" min="0" name="aleatoire" id="aleatoire" value="<?php echo $quiz['aleatoire'] ?>" />
            <p class="help-block">Le fait de choisir zéro permet d'afficher toutes les questions et dans l'ordre (mode aléatoire désactivé).</p>
        </div>
    </div>
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>

<hr />

<div>
	<a href="<?php echo $view['router']->path('zco_quiz_newQuestion', ['quizId' => $quiz['id']]) ?>" class="btn">
        <i class="icon-plus"></i> Ajouter une question
    </a>
    <a href="<?php echo $view['router']->path('zco_quiz_deleteQuiz', ['id' => $quiz['id']]) ?>" class="btn">
        <i class="icon-trash"></i> Supprimer le quiz
    </a>
    <a href="<?php echo $view['router']->path('zco_quiz_publishQuiz', ['id' => $quiz['id'], 'status' => (int)!$quiz->visible, 'tk' => $_SESSION['token']]) ?>" class="btn">
        <?php if ($quiz->visible): ?>
            <i class="icon-stop"></i> Dépublier le quiz
        <?php else: ?>
            <i class="icon-play"></i> Publier le quiz
        <?php endif ?>
    </a>
</div>

<?php if (count($questions) > 0){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Description</th>
			<th>Création</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($questions as $question){ ?>
		<tr>
			<td>
				<?php echo $view['messages']->parse($question['question']); ?>

				<ul>
					<?php for ($i = 1 ; $i <= 4 ; $i++){ ?>
					<?php if (!empty($question['reponse'.$i])){ ?>
					<li<?php if ($question['reponse_juste'] == $i) echo ' class="gras vertf"' ?>>
						<?php echo $view['messages']->parse($question['reponse'.$i]) ?>
					</li>
					<?php } } ?>
				</ul>
			</td>
			<td class="center">
				<?php echo dateformat($question['date']); ?> par <?php echo $question->Utilisateur ?>
			</td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_quiz_editQuestion', ['id' => $question['id']]) ?>">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
                <a href="<?php echo $view['router']->path('zco_quiz_deleteQuestion', ['id' => $question['id']]) ?>">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
                <a href="<?php echo $view['router']->path('zco_quiz_moveQuestion', ['id' => $question['id']]) ?>">
					<img src="/pix.gif" class="fff folder_go" title="Déplacer" alt="Déplacer"/>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucune question dans ce quiz.</p>
<?php } ?>
