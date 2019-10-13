<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Statistiques d'utilisation du quiz</h1>

<p>
	Voici vos statistiques d'utilisation du quiz. Vous pourrez y retrouver un
	historique des notes obtenues, avec la date d'obtention et le quiz (ainsi
	que sa difficulté), avec des moyennes.
</p>

<?php if ($lastNotes){ ?>
<h2>Note moyenne</h2>
<p>
	La note moyenne obtenue est de <strong><?php echo round($avgNote, 2); ?>/20</strong>
	sur <?php echo $nbNotes ?> participation<?php echo pluriel($nbNotes) ?>.
</p><br />

<h2>Histogramme représentant le nombre d'obtentions de chaque note</h2>
<img src="<?php echo $view['router']->path('zco_quiz_myStatsChart') ?>" alt="Graphique des notes obtenues au quiz" />

<h2>30 dernières notes</h2>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Quiz</th>
			<th>Difficulté</th>
			<th>Date</th>
			<th>Note</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($lastNotes as $score){ ?>
		<tr>
			<td>
                <a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $score['quiz_id'], 'slug' => rewrite($score['quiz_nom'])]) ?>">
                    <?php echo htmlspecialchars($score['quiz_nom']); ?>
                </a>
            </td>
			<td class="center"><?php echo \Quiz::LEVELS[$score['quiz_difficulte']] ?></td>
			<td><?php echo dateformat($score['date']); ?></td>
			<td class="center"><?php echo $score['note']; ?>/20</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Vous n'avez encore jamais participé au quiz. <a href="<?php echo $view['router']->path('zco_quiz_index') ?>">Me rendre à la liste des quiz !</a></p>
<?php } ?>
