<p class="centre italique"><a href="/quiz/">Accéder aux quiz</a></p>
<?php
if($ListerQuizFrequentes || $ListerQuizNouveaux || $QuizHasard)
{
	echo '<ul>';
	if($ListerQuizFrequentes)
	{
		echo '<li>Quiz les plus fréquentés<ul class="lightning">';
		foreach($ListerQuizFrequentes as $quiz)
		{
			echo '<li><a href="/quiz/quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html" title="'.htmlspecialchars($quiz['description']).'">'.htmlspecialchars($quiz['nom']).'</a> ('.$quiz['nb_questions'].')</li>';
		}
		echo '</ul></li>';
	}
	if($ListerQuizNouveaux)
	{
		echo '<li>Derniers ajouts de questions<ul class="add">';
		foreach($ListerQuizNouveaux as $quiz)
		{
			echo '<li><a href="/quiz/quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html" title="'.htmlspecialchars($quiz['description']).'">'.htmlspecialchars($quiz['nom']).'</a> ('.$quiz['nb_questions'].')</li>';
		}
		echo '</ul></li>';
	}
	if($QuizHasard)
	{
		echo '<li>Un quiz au hasard<ul class="wand">';
		echo '<li><a href="/quiz/quiz-'.$QuizHasard['id'].'-'.rewrite($QuizHasard['nom']).'.html" title="'.htmlspecialchars($QuizHasard['description']).'">'.htmlspecialchars($QuizHasard['nom']).'</a> ('.$QuizHasard['nb_questions'].')</li>';
		echo '</ul></li>';
	}
	echo '</ul>';
}
else
	echo '<p>Aucun quiz n\'a été trouvé.</p>';
?>