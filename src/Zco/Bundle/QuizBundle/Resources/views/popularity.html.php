<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Popularité des quiz</h1>

<p>
    Voici la liste de tous les quiz du site, classés par popularité, c'est-à-dire
    le nombre total de soumissions pour ce quiz. Un clic sur le titre du quiz
    vous amène à la page avec toutes les statistiques détaillées concernant
    ce quiz.
</p>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Quiz</th>
        <th>Mise en ligne</th>
        <th>Validations par des membres</th>
        <th>Validations par des visiteurs</th>
        <th>Validations totales</th>
        <th>Note moyenne</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($quizList as $quiz) { ?>
        <tr>
            <td>
                <a href="<?php echo $view['router']->path('zco_quiz_stats', ['quizId' => $quiz['id']]) ?>">
                    <?php echo htmlspecialchars($quiz['nom']) ?>
                </a>
            </td>
            <td class="center"><?php echo dateformat($quiz['date']) ?></td>
            <td class="center"><?php echo $view['humanize']->numberformat($quiz['validations_membres'], 0) ?></td>
            <td class="center"><?php echo $view['humanize']->numberformat($quiz['validations_visiteurs'], 0) ?></td>
            <td class="center"><?php echo $view['humanize']->numberformat($quiz['validations_totales'], 0) ?></td>
            <td class="center">
                <?php if ($quiz['validations_totales'] > 0) { ?>
                    <?php echo $view['humanize']->numberformat($quiz['note_moyenne']) ?>/20
                <?php } else echo '-' ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>