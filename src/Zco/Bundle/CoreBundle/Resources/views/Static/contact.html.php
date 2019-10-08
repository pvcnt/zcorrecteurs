<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoCoreBundle:Static:aboutTabs.html.php', array('currentTab' => 'contact')) ?>

<p class="good">
    Si vous avez besoin de joindre <a href="<?php echo $view['router']->path('zco_about_team') ?>">l’équipe du site</a>
    de manière personnelle, nous vous invitons à utiliser le formulaire
    ci-dessous et y formuler librement votre demande. Merci de sélectionner
    la raison la plus appropriée afin d’accélérer le traitement de votre requête.
</p>

<form method="post" action="" class="form-horizontal">
    <legend>Demande de contact</legend>
    <?php echo $view['form']->row($form['raison']) ?>
    <?php echo $view['form']->row($form['sujet']) ?>
    <?php echo $view['form']->row($form['nom']) ?>

    <?php if (verifier('connecte')): ?>
        <div class="control-group">
            <label for="pseudo" class="control-label">Pseudo sur le site *</label>
            <div class="controls">
                <input type="text" disabled="disabled" value="<?php echo htmlspecialchars($_SESSION['pseudo']) ?>"/>
            </div>
        </div>
    <?php endif; ?>

    <?php echo $view['form']->row($form['courriel']) ?>
    <?php echo $view['form']->row($form['message']) ?>

    <?php echo $view['form']->rest($form) ?>

    <div class="form-actions">
        <input type="submit" value="Envoyer" class="btn btn-primary"/>
    </div>
</form>