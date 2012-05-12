<?php $view->extend('::layouts/default.html.php') ?>
<h1>Popularité des quiz</h1>

<p>
	Voici la liste de tous les quiz du site, classés par popularité, c'est-à-dire
	le nombre total de soumissions pour ce quiz. Un clic sur le titre du quiz
	vous amène à la page avec toutes les statistiques détaillées concernant
	ce quiz.
</p>

<table class="UI_items">
	<thead>
		<tr>
			<th>Quiz</th>
			<th>Mise en ligne</th>
			<th>Questions</th>
			<th>Validations par des membres</th>
			<th>Validations par des visiteurs</th>
			<th>Validations totales</th>
			<th>Note moyenne</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach ($listeQuiz as $quiz){ ?>
		<tr>
			<td>
				<a href="statistiques-<?php echo $quiz['id'] ?>.html">
					<?php echo htmlspecialchars($quiz['nom']) ?>
				</a>
			</td>
			<td class="centre"><?php echo dateformat($quiz['date']) ?></td>
			<td class="centre"><?php echo $quiz['nb_questions'] ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($quiz['validations_membres'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($quiz['validations_visiteurs'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($quiz['validations_totales'], 0) ?></td>
			<td class="centre">
				<?php if ($quiz['validations_totales'] > 0){ ?>
				<?php echo $view['humanize']->numberformat($quiz['note_moyenne']) ?>/20
				<?php } else echo '-' ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>