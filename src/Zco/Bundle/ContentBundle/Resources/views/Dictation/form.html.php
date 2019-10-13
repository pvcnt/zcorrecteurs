<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
    <?php echo $view['form']->errors($form) ?>

    <fieldset>
        <legend>Dictée</legend>
        <?php echo $view['form']->row($form['title'], ['widget_attr' => ['class' => 'input-xxlarge']]) ?>
        <?php echo $view['form']->row($form['level'], ['widget_attr' => ['help' => 'Choisissez la difficulté de votre dictée.']]) ?>
        <?php echo $view['form']->row($form['estimated_time'], ['widget_attr' => ['help' => 'Temps indicatif pour faire cette dictée.']]) ?>
        <?php echo $view['form']->row($form['text'], ['widget_attr' => ['help' => 'Cochez cette case pour que la dictée soit rendue publique.']]) ?>
        <?php echo $view['form']->row($form['publish']) ?>
    </fieldset>

    <fieldset>
        <legend>Voix de lecture de la dictée</legend>
        <?php echo $view['form']->row($form['slow_voice'], ['widget_attr' => ['help' => 'Au format ogg ou mp3, taille maximale : '.sizeformat(ini_get('upload_max_filesize')).'.']]) ?>
        <?php echo $view['form']->row($form['fast_voice'], ['widget_attr' => ['help' => 'Au format ogg ou mp3, taille maximale : '.sizeformat(ini_get('upload_max_filesize')).'.']]) ?>
    </fieldset>

    <fieldset>
        <legend>Informations complémentaires</legend>
        <?php echo $view['form']->row($form['author_first_name']) ?>
        <?php echo $view['form']->row($form['author_last_name']) ?>
        <?php echo $view['form']->row($form['source'], ['widget_attr' => ['help' => 'Indiquez l\'origine du texte.']]) ?>
        <?php echo $view['form']->row($form['icon'], ['widget_attr' => ['help' => 'Icône pour votre dictée, au format jpg ou png.']]) ?>
        <?php echo $view['form']->row($form['description']) ?>
        <?php echo $view['form']->row($form['indications'], ['widget_attr' => ['help' => 'Indications au membre, comme l\'orthographe des noms propres.']]) ?>
        <?php echo $view['form']->row($form['comments'], ['widget_attr' => ['help' => 'Ce texte sera affiché avec la correction.']]) ?>
    </fieldset>

    <?php echo $view['form']->rest($form) ?>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Sauvegarder"/>
    </div>
</form>
