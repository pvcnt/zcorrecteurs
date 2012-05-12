<p>Bonjour,</p>

<p>
	Vous avez demandé à avoir un nouveau mot de passe sur le site
	<a href="<?php echo URL_SITE ?>"><?php echo URL_SITE ?></a>.<br />
	Votre nouveau mot de passe généré aléatoirement est le suivant : <strong><?php echo $mdp ?></strong>
</p>
<p>
	Pour l'utiliser à la place de votre mot de passe actuel, veuillez cliquer sur ce lien : 
	<a href="<?php echo $view['router']->generate('zco_user_session_newPassword', array('hash' => $hash), true) ?>">
		<?php echo $view['router']->generate('zco_user_session_newPassword', array('hash' => $hash), true) ?>
	</a>
</p>

<p>Merci d'utiliser nos services et à bientôt.</p>