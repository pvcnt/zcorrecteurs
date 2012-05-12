<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('currentTab' => 'accounts')) ?>

<h1>Ajouter un compte Twitter</h1>
<p style="float: right">
	<img src="/bundles/zcotwitter/img/oiseau.png" alt="Twitter"/>
</p>
<p>
	En ajoutant un compte Twitter, vous pourrez y envoyer des <em>tweets</em> depuis
	le site.
</p>
<p>
	Commencez par vous connecter sur <a href="http://twitter.com">Twitter</a>
	avec le compte en question, puis cliquez sur le bouton ci-dessous pour autoriser
	le site à y poster des <em>tweets</em>.
</p>

<p class="center">
	<a href="?token=<?php echo $_SESSION['token'] ?>">
		<img src="/bundles/zcotwitter/img/autoriser.png" alt="Autoriser"/>
	</a>
</p>
