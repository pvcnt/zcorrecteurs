<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta http-equiv="content-language" content="fr"/>
<meta name="description" content="<?php echo Page::$description; ?>"/>
<meta name="robots" content="<?php echo Page::$robots; ?>"/>
<?php $view['slots']->output('meta') ?>

<title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>

<?php $view['vitesse']->requireResource('@FOSJsRoutingBundle/Resources/public/js/router.js') ?>
<?php $view['javelin']->initBehavior('google-analytics', ['account' => $app->googleAnalyticsAccount()]) ?>

<?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
    <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css"/>
<?php endforeach ?>

<?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
<?php endforeach ?>

<link rel="alternate" type="application/atom+xml" title="Derniers billets du blog" href="/blog/flux.html" />

<link rel="icon" type="image/png" href="/favicon.png"/>
<link rel="start"
      title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !"
      href="/"/>