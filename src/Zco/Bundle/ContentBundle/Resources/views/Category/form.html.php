<form method="post" action="" class="form-horizontal">
    <?php echo $view['form']->errors($form) ?>

    <?php echo $view['form']->row($form['nom']) ?>
    <?php echo $view['form']->row($form['description']) ?>
    <?php echo $view['form']->row($form['url'], [
        'help' => 'Marqueurs : %id%, %id2%, %nom%.',
    ]) ?>
    <?php echo $view['form']->row($form['url_redir'], [
        'help' => 'Laissez vide pour ne pas créer une catégorie de redirection.',
    ]) ?>
    <?php echo $view['form']->row($form['archive']) ?>
    <?php echo $view['form']->row($form['parent']) ?>

    <?php echo $view['form']->rest($form) ?>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Enregistrer"/>
        <a href="<?php echo $view['router']->path('zco_categories_index') ?>" class="btn">Annuler</a>
    </div>
</form>