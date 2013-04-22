<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoAdBundle::_onglets.html.php') ?>
<h1>Supprimer définitivement une campagne</h1>

<form method="post" action="">
    <p class="centre">
        Êtes-vous sûr de vouloir supprimer définitivement cette campagne nommée
        <strong><?php echo htmlspecialchars($campagne['nom']) ?></strong> ?
        Notez que vous pouvez aussi changer son état à « Supprimé » ce qui aura
        pour effet de l'archiver, mais de conserver les statistiques. En la supprimant
        définitivement, vous supprimerez aussi toutes les publicités qui lui sont
        attachées, ainsi que les statistiques.
    </p>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Oui" />
        <a href="<?php echo $view['router']->generate('zco_ad_campaign', array('id' => $campagne['id'])) ?>" class="btn">Non</a>
    </div>
</form>

