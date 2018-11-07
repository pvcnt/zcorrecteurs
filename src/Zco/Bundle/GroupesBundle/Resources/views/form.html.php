<form method="post" action="" class="form-horizontal">
    <?php echo $view['form']->errors($form) ?>

    <?php echo $view['form']->row($form['nom']) ?>
    <?php echo $view['form']->row($form['logo']) ?>
    <?php echo $view['form']->row($form['logo_feminin']) ?>
    <?php echo $view['form']->row($form['sanction'], [
        'help' => 'Si activé, indique que le groupe est considéré comme une sanction.',
    ]) ?>
    <?php echo $view['form']->row($form['team'], [
        'help' => 'Si activé, indique que le groupe fait partie de l\'équipe du site.',
    ]) ?>
    <?php echo $view['form']->row($form['secondaire']) ?>

    <?php echo $view['form']->rest($form) ?>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Enregistrer"/>
        <a href="<?php echo $view['router']->path('zco_groups_index') ?>" class="btn">Annuler</a>
    </div>
</form>