<!DOCTYPE html>
<html>
<head>
    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/design.css') ?>
    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/design.js') ?>

    <?php echo $view->render('::layouts/head.html.php') ?>
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

        <?php echo $app->randomQuoteHtml() ?>
    </div> <!-- /header -->

    <div class="navbar navbar-inverse">
        <?php echo $view->render('::layouts/navbar.html.php') ?>
    </div> <!-- /navbar -->
<?php endif ?>

<div class="container<?php if (empty($xhr)): ?> main-container<?php endif ?>">
    <div class="content">
        <?php echo $view->render('::layouts/flashes.html.php') ?>

        <?php if (empty($xhr)): ?>
            <div id="postloading-area"></div>

            <ul class="breadcrumb">
                <li><?php echo implode('<span class="divider">»</span></li><li>', \Page::breadcrumb()) ?></li>
            </ul>
        <?php endif ?>

        <?php $view['slots']->output('_content') ?>
    </div>
</div> <!-- /container-fluid -->

<?php if (empty($xhr)): ?>
    <?php echo $view->render('::layouts/footer.html.php') ?>
<?php endif ?>

<?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
<?php endforeach ?>
<script type="text/javascript"
        src="<?php echo $view['router']->path('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
<?php echo $view['javelin']->renderHTMLFooter() ?>
</body>
</html>