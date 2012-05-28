<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta name="description" content="<?php echo Page::$description; ?>" />
		<meta http-equiv="content-language" content="fr" />
		<meta name="robots" content="<?php echo Page::$robots; ?>" />
		<meta name="language" content="fr" />

		<title><?php echo Page::$titre; ?></title>

		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/global.css') ?>
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/global.js') ?>
        
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
		<div id="body">
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
				</div>
			</div>

			<div id="page">
			<div id="content">
			<?php
			/* Affichage de l'éventuel message de maintenance */
			if ($maintenance)
			{
				afficher_erreur('Attention, le site est actuellement en maintenance !');
			}

			/* Affichage des messages éventuels en haut de la page */
			if (!empty($_SESSION['erreur']))
			{
				foreach($_SESSION['erreur'] as $erreur)
				{
					afficher_erreur($erreur);
				}
				$_SESSION['erreur'] = array();
			}

			if (!empty($_SESSION['message']))
			{
				foreach($_SESSION['message'] as $message)
				{
					afficher_message($message);
				}
				$_SESSION['message'] = array();
			}
			?>
			
			<?php echo $view['ui']->breadcrumb('legacy') ?>

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