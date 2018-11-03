<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
    <?php echo $view->render('::layouts/head.html.php') ?>

    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/global.css') ?>
    <?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/global.js') ?>
</head>

<body>
<div id="body">
    <div id="header">
        <div id="title">
            <div id="title-oreilles">
                <a href="/"
                   title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                    zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                </a>
            </div>
            <div id="title-zcorrecteurs">
                <a href="/"
                   title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !">
                    zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !
                </a>
            </div>
        </div>
    </div>

    <div id="page">
        <div id="content">
            <?php
            /* Affichage des messages éventuels en haut de la page */
            if (!empty($_SESSION['erreur'])) {
                foreach ($_SESSION['erreur'] as $erreur) {
                    echo '<p class="UI_infobox">' . $erreur . '</p>';
                }
                $_SESSION['erreur'] = array();
            }

            if (!empty($_SESSION['message'])) {
                foreach ($_SESSION['message'] as $message) {
                    echo '<p class="UI_infobox">' . $message . '</p>';
                }
                $_SESSION['message'] = array();
            }
            ?>

            <?php $view['slots']->output('_content') ?>
        </div>
    </div>
</div>

<?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
<?php endforeach ?>

<?php echo $view['javelin']->renderHTMLFooter() ?>
</body>
</html>