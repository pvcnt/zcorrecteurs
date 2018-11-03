<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/design.css') ?>
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/js/design.js') ?>

        <?php echo $view->render('::layouts/head.html.php') ?>
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
            <?php echo $view->render('::layouts/navbar.html.php') ?>
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