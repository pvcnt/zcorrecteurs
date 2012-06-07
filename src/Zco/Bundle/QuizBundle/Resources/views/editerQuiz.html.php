<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosQuiz['nom']) ?></h1>

<?php if (verifier('quiz_editer') || ($InfosQuiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_siens'))){ ?>

<form method="post" action="">
	<fieldset>
		<legend>Modifier le quiz</legend>
		<label for="nom">Nom : </label>
		<input type="text" name="nom" id="nom" size="40" value="<?php echo htmlspecialchars($InfosQuiz['nom']); ?>" /><br />

		<label for="description">Description : </label>
		<input type="text" name="description" id="description" size="80" value="<?php echo htmlspecialchars($InfosQuiz['description']); ?>" /><br />

		<label for="difficulte">Difficulté : </label>
		<select name="difficulte" id="difficulte">
			<?php foreach ($Difficultes as $cle => $valeur){ ?>
			<option value="<?php echo $cle; ?>"<?php if($InfosQuiz['difficulte'] == $valeur) echo ' selected="selected"'; ?>>
				<?php echo htmlspecialchars($valeur); ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="categorie">Catégorie : </label>
		<select name="categorie" id="categorie">
			<?php foreach ($ListerCategories as $categorie){ ?>
			<option value="<?php echo $categorie['cat_id']; ?>"<?php if($InfosQuiz['categorie_id'] == $categorie['cat_id']) echo ' selected="selected"'; ?>>
				<?php echo htmlspecialchars($categorie['cat_nom']); ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="aleatoire">Nombre de réponses choisies dans un ordre aléatoire : </label>
		<select name="aleatoire" id="aleatoire">
                        <option value="0" <?php if($InfosQuiz['aleatoire'] == 0) echo 'selected' ?>>0</option>
		<?php for($i = 2; $i <= 200; $i++) { ?>
			<option value="<?php echo $i ?>" <?php if($InfosQuiz['aleatoire'] == $i) echo 'selected' ?>><?php echo $i ?></option>
		<?php } ?>
		</select>
		<em>Le fait de choisir zéro permet d'afficher toutes les questions et dans l'ordre (mode aléatoire désactivé).</em>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>

<hr />
<?php } ?>
<p class="gras centre">
	<?php if (verifier('quiz_ajouter_questions') || ($InfosQuiz['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_ajouter_questions_siens'))){ ?>
	<a href="ajouter-question-<?php echo $_GET['id']; ?>.html">Ajouter une question</a><br />
	<?php } ?>
</p>

<?php if (count($ListeQuestions) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 60%;">Description</th>
			<th style="width: 20%;">Création</th>
			<th style="width: 20%;">Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($ListeQuestions as $question){ ?>
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
			<td class="centre">
				<?php echo dateformat($question['date']); ?> par <?php echo $question->Utilisateur ?>
			</td>
			<td class="centre">
				<?php if (verifier('quiz_editer_questions') || ($question['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_editer_ses_questions'))){ ?>
				<a href="editer-question-<?php echo $question['id']; ?>.html">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
				<?php }
				if (verifier('quiz_supprimer_questions') || ($question['utilisateur_id'] == $_SESSION['id'] && verifier('quiz_supprimer_ses_questions'))){ ?>
				<a href="supprimer-question-<?php echo $question['id']; ?>.html">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
				<?php }
				if (verifier('quiz_supprimer_questions') && verifier('quiz_ajouter_questions')) { ?>
				<a href="deplacer-question-<?php echo $question['id'] ?>.html">
					<img src="/pix.gif"
					     class="fff folder_go"
					     title="Déplacer"
					     alt="Déplacer"/>
				</a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucune question dans ce quiz.</p>
<?php } ?>

<p class="gras centre">Retour à <a href="gestion.html">la gestion des quiz</a></p>
