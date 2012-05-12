<?php $view->extend('::layouts/default.html.php') ?>

<h1>
	Statistiques d'utilisation du quiz
	<?php if($_SESSION['id'] != $_GET['id']){ ?>
	de <?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?>
	<?php } ?>
</h1>

<p>
	Voici vos statistiques d'utilisation du quiz. Vous pourrez y retrouver un
	historique des notes obtenues, avec la date d'obtention et le quiz (ainsi
	que sa difficulté), avec des moyennes.
</p><br />

<?php if ($Statistiques['notes']){ ?>
<h2>Note moyenne</h2>
<p>
	La note moyenne obtenue est de <strong><?php echo round($Statistiques['note_moy'], 2); ?>/20</strong>
	sur <?php echo $Statistiques['nb_scores']; ?> participation<?php echo pluriel($Statistiques['nb_scores']); ?>.
</p><br />

<h2>Histogramme représentant le nombre d'obtentions de chaque note</h2>
<img src="/quiz/graphique-stats.html" alt="Graphique des notes obtenues au quiz" />
<br /><br />

<h2>30 dernières notes</h2>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 40%;">Quiz</th>
			<th style="width: 20%;">Difficulté</th>
			<th style="width: 20%;">Date</th>
			<th style="width: 20%;">Note</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($Statistiques['notes'] as $score){ ?>
		<tr>
			<td><a href="/quiz/quiz-<?php echo $score->Quiz['id']; ?>.html"><?php echo htmlspecialchars($score->Quiz['nom']); ?></a></td>
			<td class="centre"><?php echo htmlspecialchars($score->Quiz['difficulte']); ?></td>
			<td><?php echo dateformat($score['date']); ?></td>
			<td class="centre"><?php echo $score['note']; ?>/20</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Vous n'avez encore jamais participé au quiz. <a href="/quiz/">Me rendre à la liste des quiz !</a></p>
<?php } ?>
