<p class="centre italique"><a href="<?php echo $view['router']->path('zco_quiz_index') ?>">Accéder aux quiz</a></p>

<?php if($ListerQuizFrequentes || $ListerQuizNouveaux || $QuizHasard): ?>
	<ul>
	<?php if($ListerQuizFrequentes): ?>
		<li>Quiz les plus fréquentés<ul class="lightning">
		<?php foreach($ListerQuizFrequentes as $quiz): ?>
			<li><a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]) ?>"
                   title="<?php echo htmlspecialchars($quiz['description']) ?>">
                    <?php echo htmlspecialchars($quiz['nom']) ?>
            </a></li>
		<?php endforeach; ?>
		</ul></li>
	<?php endif ?>
	<?php if($ListerQuizNouveaux): ?>
		<li>Derniers ajouts de questions<ul class="add">
		<?php foreach($ListerQuizNouveaux as $quiz): ?>
			<li><a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]) ?>"
                   title="<?php echo htmlspecialchars($quiz['description']) ?>">
                    <?php echo htmlspecialchars($quiz['nom']) ?>
            </a></li>
		<?php endforeach ?>
		</ul></li>
    <?php endif ?>
	<?php if($QuizHasard): ?>
		<li>Un quiz au hasard<ul class="wand">
		<li><a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $QuizHasard['id'], 'slug' => rewrite($QuizHasard['nom'])]) ?>"
               title="<?php echo htmlspecialchars($QuizHasard['description']) ?>">
                <?php echo htmlspecialchars($QuizHasard['nom']) ?>
        </a></li>
		</ul></li>
	<?php endif ?>
	</ul>
<?php else: ?>
	<p>Aucun quiz n'a été trouvé.</p>
<?php endif ?>