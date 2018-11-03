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

        <link rel="alternate" type="application/atom+xml" title="Derniers billets du blog" href="/blog/flux.html" />
		
		<link rel="icon" type="image/png" href="/favicon.png" />
		<link rel="start" title="zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !" href="/" />
	</head>

	<body>
	    <?php if (!$xhr): ?>
	    <div id="header" style="margin-bottom: 18px;">
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
	<?php endif ?>

		<div class="container">
		    <div class="row">
    		    <div class="span3">
    		        <div class="well sidebar-nav">
    		            <ul class="nav nav-list">
    		                <li<?php if ($currentPage === 'index') echo ' class="active"' ?>>
    		                    <a href="<?php echo $view['router']->path('zco_file_index', compact('textarea', 'input')) ?>">
    		                        Envoyer des fichiers
    		                    </a>
    		                </li>
    		                <li class="nav-header">Dossiers intelligents</li>
    		                <?php foreach ($smartFolders as $folder): ?>
    		                    <?php if (!$folder['hidden'] || ($currentFolder && $currentFolder['id'] == $folder['id'])): ?>
    		                    <li<?php if ($currentFolder && $currentFolder['id'] == $folder['id']) echo ' class="active"' ?>>
    		                        <a href="<?php echo $view['router']->path('zco_file_folder', array('id' => $folder['id'], 'entities' => $currentContentFolder ? $currentContentFolder['id'] : '', 'textarea' => $textarea, 'input' => $input)) ?>">
    		                            <i class="icon-<?php echo $folder['icon'] ?>"></i>
    		                            <?php echo htmlspecialchars($folder['name']) ?>
    		                        </a>
    		                    </li>
		                        <?php endif ?>
    		                <?php endforeach ?>

							<li class="nav-header">Filtres par contenu</li>
    		                <?php foreach ($contentFolders as $folder): ?>
    		                    <?php if (!$folder['hidden'] || ($currentFolder && $currentFolder['id'] == $folder['id'])): ?>
    		                    <li<?php if ($currentContentFolder && $currentContentFolder['id'] == $folder['id']) echo ' class="active"' ?>>
									<?php if ($currentContentFolder && $currentContentFolder['id'] == $folder['id']): ?>
									<a style="float: right;" 
									   title="Retirer le filtre" 
									   href="<?php echo $view['router']->path('zco_file_folder', array(
											'id' => $currentFolder ? $currentFolder['id'] : FileTable::FOLDER_ALL, 
											'textarea' => $textarea, 
											'input' => $input,
										)) ?>"><i class="icon-remove"></i></a>
									<?php endif ?>
    		                        <a href="<?php echo $view['router']->path('zco_file_folder', array(
										'id' => $currentFolder ? $currentFolder['id'] : FileTable::FOLDER_ALL, 
										'entities' => $folder['id'], 
										'textarea' => $textarea, 
										'input' => $input,
									)) ?>">
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
                    <?php echo $view->render('::layouts/flashes.html.php') ?>
		
        			<?php $view['slots']->output('_content') ?>
    		    </div>
    		</div> <!-- /row-fluid -->
	    </div> <!-- /container-fluid -->
		
		<?php if (!$xhr): ?>
            <?php echo $view->render('::layouts/_footer.html.php') ?>
	    <?php endif ?>
	    
		<?php foreach ($view['vitesse']->javascripts() as $assetUrl): ?>
		    <script type="text/javascript" src="<?php echo $assetUrl ?>"></script>
		<?php endforeach ?>
		<script type="text/javascript" src="/bundles/fosjsrouting/js/router.js"></script>
        <script type="text/javascript" src="<?php echo $view['router']->path('fos_js_routing_js', array('callback' => 'fos.Router.setData')) ?>"></script>
		
		<?php echo $view['javelin']->renderHTMLFooter() ?>
	</body>
</html>