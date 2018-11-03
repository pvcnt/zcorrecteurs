<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta name="description" content="<?php echo Page::$description; ?>" />
		<meta http-equiv="content-language" content="fr" />
		<meta name="robots" content="<?php echo Page::$robots; ?>" />
		<meta name="language" content="fr" />

		<title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>

		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/design.css') ?>
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/design.js') ?>
        
		<?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
		    <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
		<?php endforeach ?>
    	
		<?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
		    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
		<?php endforeach ?>

        <link rel="alternate" type="application/atom+xml" title="Derniers billets du blog" href="/blog/flux.html" />
		
		<link rel="icon" type="image/png" href="/favicon.png" />
		<link rel="start" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !" href="/" />
	</head>

	<body>
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
		</div> <!-- /header -->
		
		<div class="navbar navbar-static">
            <?php echo $view->render('::layouts/_navbar.html.php', ['adminCount' => $adminCount, 'searchSection' => $searchSection]) ?>
		</div> <!-- /navbar -->
		
		<div class="container">
			<?php echo $view->render('::layouts/flashes.html.php') ?>

			<?php $view['slots']->output('_content') ?>
		</div> <!-- /container -->

        <?php echo $view->render('::layouts/_footer.html.php') ?>

		<?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
		    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
		<?php endforeach ?>
		<?php echo $view['javelin']->renderHTMLFooter() ?>
	</body>
</html>