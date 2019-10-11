<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Quiz sur la langue française et la culture générale</h1>

<p>
    Voici la liste de tous les quiz du site, classés par catégorie. Ils vous
    permettent de tester vos connaissances dans divers domaines liés à la langue
    française ou encore la culture générale. Notez que si vous êtes inscrit et
    connecté, vous disposez d'un suivi complet de vos notes ainsi que de graphiques
    pour suivre votre progression !
</p>

<div class="alert alert-info">
    À noter que si vous souhaitez proposer vos propres questions afin d'enrichir
    ces quiz, vous pouvez nous les soumettre dans
    <a href="<?php echo $view['router']->url('zco_forum_showTopic', ['id' => 871, 'slug' => 'quiz-proposez-vos-questions']) ?>">ce sujet
    réservé à cet usage</a>.
</div>

<?php if (verifier('connecte')) { ?>
    <p class="bold center">
        <a href="<?php echo $view['router']->path('zco_quiz_myStats') ?>">Accéder à mes statistiques personnelles</a>
    </p>
<?php } ?>

<?php if (!is_null($pinnedQuiz)) { ?>
    <div class="well" style="width: 50%;">
        <?php if (!empty($pinnedQuiz['image'])) { ?>
            <a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $pinnedQuiz['id'], 'slug' => rewrite($pinnedQuiz['nom'])]) ?>">
                <img class="flot_droite" src="<?php echo htmlspecialchars($pinnedQuiz['image']); ?>" alt=""/>
            </a>
        <?php } ?>

        Le quiz suivant de la catégorie « <?php echo htmlspecialchars($pinnedQuiz['Categorie']['nom']); ?> »
        est actuellement mis en valeur par l'équipe du site :<br/><br/>

        <div>
            <strong><a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $pinnedQuiz['id'], 'slug' => rewrite($pinnedQuiz['nom'])]) ?>">
                    <?php echo htmlspecialchars($pinnedQuiz['nom']); ?>
                </a></strong>
            <?php if (!empty($pinnedQuiz['description'])) { ?><br/>
                <?php echo htmlspecialchars($pinnedQuiz['description']); ?>
            <?php } ?>
        </div>
        <div style="clear: right;"></div>
    </div>
<?php } ?>

<?php
$current = null;
if (count($quizList) > 0):
    foreach ($quizList as $key => $quiz):
        if ($quiz['categorie_id'] != $current):
            $current = $quiz['categorie_id'];
            if ($key != 0) {
                echo '</tbody></table><br />';
            }
            ?>
            <h2 id="c<?php echo $quiz->Categorie['id'] ?>"><?php echo htmlspecialchars($quiz->Categorie['nom']) ?></h2>

            <table class="table table-striped">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Création</th>
                <th>Difficulté</th>
            </tr>
            </thead>
            <tbody>
        <?php endif; ?>
        <tr>
            <td>
                <a href="<?php echo $view['router']->path('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])]) ?>">
                    <?php echo htmlspecialchars($quiz['nom']); ?>
                </a>
                <?php if (!empty($quiz['description'])): ?><br/>
                    <em><?php echo htmlspecialchars($quiz['description']) ?></em>
                <?php endif ?>
            </td>
            <td><?php echo dateformat($quiz['date']) ?></td>
            <td>
                <span style="float: right; margin-right: 5px;"><?php echo $quiz['difficulte'] ?></span>
                <?php echo str_repeat(
                    '<img src="/bundles/zcoquiz/img/etoile.png" alt="' . $quiz['difficulte'] . '" title="' . $quiz['difficulte'] . '" />',
                    $quiz->getNumericLevel() + 1
                ) ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
    </table>

<?php else: ?>
    <p>Aucun quiz n'est visible.</p>
<?php endif ?>