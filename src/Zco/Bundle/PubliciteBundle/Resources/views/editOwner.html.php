<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php') ?>
<h1>Changer le créateur de la campagne : <?php echo htmlspecialchars($campagne['nom']) ?></h1>

<p>
    Seul le créateur d'une campagne a accès aux options de modification du
    ciblage, au lancement, à la mise en pause d'une campagne et à la
    visualisation des statistiques.
</p>

<?php if ($campagne['utilisateur_id'] == $_SESSION['id']) { ?>
    <p class="bold">
        Si vous changez le créateur de la campagne, vous perdrez tout
        contrôle sur votre campagne !
    </p>
<?php } ?>

<form method="post" action="" class="form-horizontal">
    <div class="control-group">
        <label for="pseudo" class="control-label">Nouveau créateur</label>
        <div class="controls">
            <input type="text" name="pseudo" id="pseudo" size="40" 
                   value="<?php echo htmlspecialchars($campagne->Utilisateur['pseudo']) ?>" 
            />
        </div>
    </div>
    
    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Changer le propriétaire" />
        <a href="<?php echo $view['router']->generate('zco_ads_campaign', array('id' => $campagne['id'])) ?>" class="btn">Annuler</a>
    </div>

    <?php
    $view['javelin']->initBehavior('autocomplete', array(
        'id'       => 'pseudo',
        'callback' => $view['router']->generate('zco_user_api_searchUsername'),
    ))
    ?>
</form>