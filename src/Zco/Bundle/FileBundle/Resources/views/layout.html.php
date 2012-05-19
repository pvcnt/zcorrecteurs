<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Language" content="fr" />
		<meta name="language" content="fr" />
		<meta http-equiv="content-language" content="fr" />
		<meta name="description" content="<?php echo Page::$description; ?>" />
		<meta name="robots" content="<?php echo Page::$robots; ?>" />

		<title><?php echo str_replace(array(' '), ' ', Page::$titre); ?></title>
		
		<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/design.css') ?>
		<?php $view['vitesse']->requireResource('@ZcoFileBundle/Resources/public/css/fichiers.css') ?>
        
		<?php foreach ($view['vitesse']->stylesheets() as $assetUrl): ?>
		    <link rel="stylesheet" href="<?php echo $assetUrl ?>" media="screen" type="text/css" />
		<?php endforeach ?>
    	
		<?php foreach ($view['vitesse']->javascripts(array('mootools', 'mootools-more')) as $assetUrl): ?>
		    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
		<?php endforeach ?>
		
		<?php echo $view['vitesse']->renderFeeds() ?>
		
		<link rel="icon" type="image/png" href="/favicon.png" />
		<link rel="start" title="zCorrecteurs.fr - Corrections orthographiques, forum, blog, quiz, et bien plus encore !" href="/" />
	</head>

	<body>
	    <?php if (!$xhr): ?>
	    <div id="header" style="margin-bottom: 18px;">
			<div id="header-oreilles">
				<a href="/" title="zCorrecteurs.fr - Corrections orthographiques, forum, blog, quiz, et bien plus encore !">
					zCorrecteurs.fr - Corrections orthographiques, forum, blog, quiz, et bien plus encore !
				</a>
			</div>
			<div id="header-zcorrecteurs">
				<a href="/" title="zCorrecteurs.fr - Corrections orthographiques, forum, blog, quiz, et bien plus encore !">
					zCorrecteurs.fr - Corrections orthographiques, forum, blog, quiz, et bien plus encore !
				</a>
			</div>
		</div> <!-- /header -->
	<?php endif ?>

		<div class="container">
		    <div class="row">
    		    <div class="span3">
    		        <div class="well sidebar-nav">
    		            <ul class="nav nav-list">
    		                <li<?php if ($currentPage === 'index') echo ' class="active"' ?>>
    		                    <a href="<?php echo $view['router']->generate('zco_file_index', compact('textarea', 'input')) ?>">
    		                        Envoyer des fichiers
    		                    </a>
    		                </li>
    		                <?php /*<li<?php if ($currentPage === 'commons') echo ' class="active"' ?>>
    		                    <a href="<?php echo $view['router']->generate('zco_file_commons', compact('textarea', 'input')) ?>">
    		                        Rechercher sur Commons
    		                    </a>
    		                </li>*/ ?>
    		                
    		                <li class="nav-header">Dossiers intelligents</li>
    		                <?php foreach ($smartFolders as $folder): ?>
    		                    <?php if (!$folder['hidden'] || ($currentFolder && $currentFolder['id'] == $folder['id'])): ?>
    		                    <li<?php if ($currentFolder && $currentFolder['id'] == $folder['id']) echo ' class="active"' ?>>
    		                        <a href="<?php echo $view['router']->generate('zco_file_folder', array('id' => $folder['id'], 'textarea' => $textarea, 'input' => $input)) ?>">
    		                            <i class="icon-<?php echo $folder['icon'] ?>"></i>
    		                            <?php echo htmlspecialchars($folder['name']) ?>
    		                        </a>
    		                    </li>
		                        <?php endif ?>
    		                <?php endforeach ?>
    		            </ul>
    		        </div>
    		    </div>
    			<div class="span9 content">
    		        <?php
        			/* Affichage de l'éventuel message de maintenance */
        			if ($maintenance)
        			{
        				echo '<div class="alert alert-error">Attention, le site est actuellement en maintenance !</div>';
        			}

        			/* Affichage des messages éventuels en haut de la page */
        			if (!empty($_SESSION['erreur']))
        			{
        				foreach ($_SESSION['erreur'] as $erreur)
        				{
        					echo '<div class="alert alert-error">'.$erreur.'</div>';
    					}
        				$_SESSION['erreur'] = array();
        			}

        			if (!empty($_SESSION['message']))
        			{
        				foreach ($_SESSION['message'] as $message)
        				{
        					echo '<div class="alert alert-success">'.$message.'</div>';
    					}
        				$_SESSION['message'] = array();
        			}
        			?>
		
        			<?php $view['slots']->output('_content') ?>
    		    </div>
    		</div> <!-- /row-fluid -->
	    </div> <!-- /container-fluid -->
		
		<?php if (!$xhr): ?>
		<div id="footer">
			<div class="left">
			    <span>Site fièrement édité par</span>
				<a href="http://www.corrigraphie.org" title="Ce site est hébergé et édité par l’association Corrigraphie.">Corrigraphie</a>
			</div>
			
			<div class="right bloc_partenaires">
				<a href="http://www.siteduzero.com" title="Le Site du Zéro, site communautaire de tutoriels gratuits pour débutants : programmation, création de sites Web, Linux..." id="pub-19">Site du Zéro</a>
				<?php $view['cache']->increment('pub_nbv-19') ?>
			</div>
			
			<div class="center">
				<p><a href="#" title="Remonter en haut de page" id="toplink">Haut de page</a></p>
				
				<?php echo $view['ui']->footer(1); ?>
				<?php echo $view['ui']->footer(2); ?>
				<?php echo $view['ui']->footer(3, array('childrenAttributes' => array('class' => 'links bloc_partenaires'), 'preHtml' => 'Partenaires : ')); ?>
			</div>
		</div>
	    <?php endif ?>
	    
		<?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
		    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
		<?php endforeach ?>
		<script type="text/javascript" src="/bundles/fosjsrouting/js/router.js"></script>
        <script type="text/javascript" src="<?php echo $view['router']->generate('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
		
		<?php echo $view['javelin']->renderHTMLFooter() ?>
	</body>
</html>