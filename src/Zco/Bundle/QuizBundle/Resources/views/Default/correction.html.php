<?php $view->extend('::layouts/bootstrap.html.php') ?>

<span style="float: right;">
	<?php if (verifier('voir_stats_generales')) { ?>
        <a href="statistiques-<?php echo $quiz['id'] ?>.html">
		<img src="/img/membres/stats_zco.png" alt="Statistiques"/>
		Statistiques
	</a>
    <?php }
    if (verifier('quiz_ajouter')) { ?>
        <a href="<?php echo $view['router']->path('zco_quiz_editQuiz', ['id' => $quiz['id']]) ?>"
           title="Modifier le quiz">
		<img src="/img/editer.png" alt="Modifier le quiz"/>
	</a>
        <a href="<?php echo $view['router']->path('zco_quiz_deleteQuiz', ['id' => $quiz['id']]) ?>"
           title="Supprimer le quiz">
		<img src="/img/supprimer.png" alt="Supprimer le quiz"/>
	</a>
    <?php } ?>
</span>

<h1>
    <?php echo htmlspecialchars($quiz['nom']); ?>
    <?php if (!empty($quiz['description'])) { ?>
        <small><?php echo htmlspecialchars($quiz['description']); ?></small>
    <?php } ?>
</h1>

<div class="alert alert-info">
    Vous avez obtenu <strong><?php echo $note ?>/20</strong>.
</div>

<?php foreach ($questions as $i => $question) { ?>
    <p class="bold">
        Question <?php echo $i + 1 ?> : <?php echo $view['messages']->parse($question['question']); ?>
    </p>
    <div class="correction">
        <?php if ($reponses[$i]['correct']) { ?>
            <div class="correction juste">
                <p class="type">C'est une bonne réponse !</p>
                <p>
                    Vous avez choisi :
                    <?php echo $view['messages']->parse($question['reponse' . $reponses[$i]['choice']]) ?>
                </p>
            </div>
        <?php } else { ?>
            <div class="correction faux">
                <p class="type">Mauvaise réponse.</p>
                <p>
                    Vous avez choisi :
                    <?php echo $view['messages']->parse($question['reponse' . $reponses[$i]['choice']]) ?>
                </p>
                <p>
                    La bonne réponse était :
                    <?php echo $view['messages']->parse($question['reponse' . $question['reponse_juste']]) ?>
                </p>
            </div>
        <?php } ?>
        <?php if (!empty($question['explication'])) { ?>
            <div class="explication">
                <?php echo $view['messages']->parse($question['explication']) ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>