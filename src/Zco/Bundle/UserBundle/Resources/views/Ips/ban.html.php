<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Bannir une adresse IP</h1>

<p>
    Cette page vous permet de bannir des adresses IPs. Cela signifie que tout membre utilisant cette adresse sera
    automatiquement redirigé vers une page de bannissement.<br/>
    <strong>Cette option est à utiliser avec précaution.</strong>
</p>

<form method="post" action="" class="form-horizontal">
    <div class="control-group">
        <label for="ip" class="control-label">Adresse IP</label>
        <div class="controls">
            <input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($ip) ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label for="duree" class="control-label">Durée en jours</label>
        <div class="controls">
            <input type="text" size="2" id="duree" name="duree" value="3"/>
            <p class="help-text">Entrez 0 pour toujours.</p>
        </div>
    </div>

    <div class="control-group">
        <label for="raison" class="control-label">Raison visible par le membre</label>
        <div class="controls">
            <?php echo $view->render('::zform.html.php', array('id' => 'raison')); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="texte" class="control-label">Raison visible par les admins</label><br/>
        <div class="controls">
            <?php echo $view->render('::zform.html.php', array('id' => 'texte')) ?>
        </div>
    </div>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Envoyer"/>
        <a href="<?php echo $view['router']->path('zco_user_ips_index') ?>" class="btn">Annuler</a>
    </div>
</form>
