<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Suppression du sujet</h1>

<form method="post" action="">
    <p>
        Êtes-vous sûr de vouloir supprimer le sujet
        <strong><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></strong>
        ainsi que les messages qu'il contient ?<br />

        La suppression entraînera la perte de
        <strong><?php echo $InfosSujet['nombre_de_messages']; ?>
        message<?php echo pluriel($InfosSujet['nombre_de_messages']); ?></strong>.
    </p>

    <p class="form-actions center">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a class="btn" href="<?php echo $view['router']->path('zco_topic_show', ['id' => $InfosSujet['sujet_id'], 'slug' => rewrite($InfosSujet['sujet_titre'])]) ?>">Non</a>
    </p>
</form>