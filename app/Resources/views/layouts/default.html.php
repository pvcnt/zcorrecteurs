<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="fr" />
        <meta name="language" content="fr" />
        <meta http-equiv="content-language" content="fr" />
        <meta name="description" content="<?php echo Page::$description; ?>" />
        <meta name="robots" content="<?php echo Page::$robots; ?>" />
        <?php $view['slots']->output('meta') ?>

        <title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>

        <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/global.css') ?>
        <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/global.js') ?>

        <?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
            <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
        <?php endforeach ?>
        <!--[if IE]>
        <?php foreach ($view['vitesse']->stylesheets(array('@ZcoCoreBundle/Resources/public/css/ie.css')) as $assetUrl): ?>
                <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
        <?php endforeach ?>
        <![endif]-->

        <?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
            <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
        <?php endforeach ?>

        <?php echo $view['vitesse']->renderFeeds() ?>

        <link rel="icon" type="image/png" href="/favicon.png" />
        <link rel="start" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !" href="/" />
    </head>

    <body>
        <div id="acces_rapide">
            <a href="#page">Aller au menu</a> -
            <a href="#content">Aller au contenu</a>
        </div>

        <div id="body">
            <?php if (empty($xhr)): ?>
                <div id="header">
                    <div id="title">
                        <div id="title-oreilles">
                            <a href="/" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                                zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                            </a>
                        </div>
                        <div id="title-zcorrecteurs">
                            <a href="/" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                                zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                            </a>
                        </div>

                        <?php echo $view['ui']->headerRight('legacy') ?>
                    </div>
                </div>
                <div id="speedbarre">
                    <?php echo $view['ui']->speedbarre('legacy') ?>
                </div>

                <div id="page">
                    <div class="sidebar sidebarleft"><?php echo $view['ui']->leftMenu('legacy') ?></div>
                    <div id="content" class="right">
                    <?php endif ?>
                    <?php
                    /* Affichage de l'éventuel message de maintenance */
                    if ($maintenance) {
                        afficher_erreur('Attention, le site est actuellement en maintenance !');
                    }

                    /* Affichage des messages éventuels en haut de la page */
                    if (!empty($_SESSION['erreur'])) {
                        foreach ($_SESSION['erreur'] as $erreur)
                            afficher_erreur($erreur);
                        $_SESSION['erreur'] = array();
                    }

                    if (!empty($_SESSION['message'])) {
                        foreach ($_SESSION['message'] as $message)
                            afficher_message($message);
                        $_SESSION['message'] = array();
                    }

                    /* Affichage de l'admin rapide éventuellement */
                    if (empty($xhr) && verifier('admin') && preference('display_admin_bar') == 1) {
                        ?>
                        <div id="admin_rapide">
                            <?php if (verifier('groupes_gerer')) { ?>
                                <div class="admin_lien">
                                    <a href="/groupes/">
                                        <img src="/pix.gif" class="fff group" alt="" /> Groupes
                                    </a>
                                </div>
                            <?php } if (verifier('blog_voir_billets_proposes')) {
                                $billets = \Container::getService('zco_admin.manager')->get('blog'); ?>
                                <div class="admin_lien">
                                    <a href="/blog/propositions.html"<?php if ($billets > 0) echo ' title="' . $billets . ' billet' . pluriel($billets) . ' en attente"'; ?>>
                                        <img src="/pix.gif" class="fff book" alt="" />
                                        <?php if ($billets > 0) echo '<strong>'; ?>Blog
                                        <?php if ($billets > 0) echo '(' . $billets . ')</strong>'; ?>
                                    </a>
                                </div>
                            <?php } if (verifier('recrutements_voir_candidatures')) {
                                $candidatures = \Container::getService('zco_admin.manager')->get('recrutement'); ?>
                                <div class="admin_lien">
                                    <a href="/recrutement/gestion.html"<?php if ($candidatures > 0) echo ' title="' . $candidatures . ' candidature' . pluriel($candidatures) . ' en attente"'; ?>>
                                        <img src="/pix.gif" class="fff door_in" alt="" />
                                        <?php if ($candidatures > 0) echo '<strong>'; ?>Recrutements
                                        <?php if ($candidatures > 0) echo '(' . $candidatures . ')</strong>'; ?>
                                    </a>
                                </div>
                            <?php } if (verifier('zcorriger') || verifier('voir_tutos_attente')) {
                                $tutos = \Container::getService('zco_admin.manager')->get('zcorrection'); ?>
                                <div class="admin_lien">
                                    <a href="/zcorrection/"<?php if ($tutos > 0) echo ' title="' . $tutos . ' tutoriel' . pluriel($tutos) . ' en attente"'; ?>>
                                        <img src="/pix.gif" class="fff pencil_green" alt="" />
                                        <?php if ($tutos > 0) echo '<strong>'; ?>zCorrection
                                        <?php if ($tutos > 0) echo '(' . $tutos . ')</strong>'; ?>
                                    </a>
                                </div>
                        <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if (empty($xhr)): ?>
                        <div id="postloading-area"></div>
                        <?php echo $view['ui']->breadcrumb('legacy') ?>
                    <?php endif ?>

                    <?php $view['slots']->output('_content') ?>

                    <?php if (empty($xhr)): ?>
                    </div>
                </div>
                <?php echo $view->render('::layouts/_footer.html.php') ?>
            <?php endif ?>

            <?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
                <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
            <?php endforeach ?>
            <script type="text/javascript" src="<?php echo $view['router']->path('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
            <?php echo $view['javelin']->renderHTMLFooter() ?>
        </div>
    </body>
</html>