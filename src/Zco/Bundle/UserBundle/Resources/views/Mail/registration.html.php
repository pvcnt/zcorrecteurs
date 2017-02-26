<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous vous êtes inscrit sur le site des
	<a href="<?php echo $view['router']->url('zco_home') ?>">zCorrecteurs</a>.<br />
	Si vous êtes bien l'auteur de cette demande, veuillez confirmer votre inscription
	en cliquant ou bien en copiant et collant le lien qui suit :
</p>

<p style="text-align: center;">
	<a href="<?php echo $view['router']->url('zco_user_session_confirm', compact('id', 'hash')) ?>">
		<?php echo $view['router']->url('zco_user_session_confirm', compact('id', 'hash')) ?>
	</a>
</p>

<p>
	En cas de problème, n'hésitez pas à
	<a href="<?php echo $view['router']->url('zco_about_contact') ?>">prendre contact avec nous</a>.<br />
	Merci de votre confiance et à bientôt !
</p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>