<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Suppression d'un compte</h1>

<form method="post" action="">
	<fieldset>
		<p>
			Êtes-vous sûr de vouloir supprimer le compte de 
			<strong><a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a></strong>
			de ce site ? Cela signifie qu'il ne pourra plus se connecter. Ses 
			messages sur seront cependant conservés. Si cela arrive parce que 
			le membre s'est mal conduit, préférez le bannissement.
		</p>

		<div class="center form-actions">
			<input class="btn btn-primary" type="submit" name="confirm" value="Oui" />
			<a class="btn" href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>">Non</a>
		</div>
	</fieldset>
</form>