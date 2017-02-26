<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('currentTab' => 'accounts')) ?>

<form method="post" action="">
	<p class="center">
		Êtes-vous sûr de vouloir supprimer le compte
		<strong><?php echo htmlspecialchars($account['nom']) ?></strong> ?
	</p>
	
	<p class="rmq attention">
		Tous les <em>tweets</em> associés seront également supprimés
		du site (pas de Twitter).
	</p>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="confirm" value="Oui" />
		<a class="btn" href="<?php echo $view['router']->path('zco_twitter_accounts') ?>">Non</a>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
	</div>
</form>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>