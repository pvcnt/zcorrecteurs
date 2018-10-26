<form method="post" class="form-horizontal" action="">
    <?php echo $view['form']->errors($form) ?>

    <?php echo $view['form']->row($form['auteur_prenom']) ?>
    <?php echo $view['form']->row($form['auteur_nom']) ?>
    <?php echo $view['form']->row($form['auteur_autres']) ?>
    <?php echo $view['form']->row($form['contenu']) ?>
    <?php echo $view['form']->row($form['statut']) ?>

    <?php echo $view['form']->rest($form) ?>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Sauvegarder" />
        <a href="<?php echo $view['router']->generate('zco_quote_index') ?>" class="btn">Annuler</a>
    </div>
</form>
