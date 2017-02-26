<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Language" content="fr" />
        <meta name="description" content="<?php echo Page::$description; ?>" />
        <meta name="robots" content="<?php echo Page::$robots; ?>" />

        <title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>

        <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/design.css') ?>
        <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/design.js') ?>

        <?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
            <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
        <?php endforeach ?>

        <?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
            <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
        <?php endforeach ?>

        <?php echo $view['vitesse']->renderFeeds() ?>

        <link rel="icon" type="image/png" href="/favicon.png" />
        <link rel="start" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !" href="/" />
    </head>

    <body>
        <?php if (empty($xhr)): ?>
            <div id="header">
                <div id="header-oreilles">
                    <a href="/" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                        zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                    </a>
                </div>
                <div id="header-zcorrecteurs">
                    <a href="/" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                        zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                    </a>
                </div>

                <?php echo $view['ui']->headerRight('bootstrap') ?>
            </div> <!-- /header -->

            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <div class="container">
                        <?php echo $view['ui']->speedbarre('bootstrap') ?>
                        <?php echo $view['ui']->speedbarreRight('bootstrap') ?>
                    </div>
                </div>
            </div> <!-- /navbar -->
        <?php endif ?>

        <div class="container-fluid<?php if (empty($xhr)): ?> main-container<?php endif ?>">
            <div class="row-fluid">
                <?php if (empty($xhr)): ?>
                    <div class="span2 sidebar">
                        <?php echo $view['ui']->leftMenu('bootstrap') ?>
                    </div>
                <?php endif ?>

                <div class="<?php echo empty($xhr) ? 'span10' : 'span12' ?> content">
                    <?php echo $view->render('::layouts/flashes.html.php', compact('maintenance')) ?>

                    <?php if (empty($xhr)): ?>
                        <div id="postloading-area"></div>

                        <?php echo $view['ui']->breadcrumb('bootstrap') ?>
                    <?php endif ?>

                    <?php $view['slots']->output('_content') ?>
                </div>
            </div> <!-- /row-fluid -->
        </div> <!-- /container-fluid -->

        <?php if (empty($xhr)): ?>
            <?php echo $view->render('::layouts/_footer.html.php') ?>
        <?php endif ?>

        <?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
            <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
        <?php endforeach ?>	
        <script type="text/javascript" src="<?php echo $view['router']->path('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
        <?php echo $view['javelin']->renderHTMLFooter() ?>
    </body>
</html>