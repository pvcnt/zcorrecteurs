<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez reçu un nouveau message privé sur le site des
	<a href="<?php echo URL_SITE ?>">zCorrecteurs</a>.<br />
	Il vous a été envoyé par <a href="<?php echo $view['router']->url('zco_user_profile', ['id' => $auteur_id, 'slug' => rewrite($auteur_pseudo)]) ?>"><?php echo htmlspecialchars($auteur_pseudo) ?></a>
	et son titre est <?php echo htmlspecialchars($titre) ?>.</p>

<p>
	<a href="<?php echo URL_SITE ?>/mp/lire-<?php echo $id ?>.html">Cliquez ici</a> pour le lire.<br />
	<a href="<?php echo URL_SITE ?>/mp/">Accueil de la messagerie privée</a>
</p>

<p>
	Si vous ne souhaitez plus recevoir d'email quand vous recevez un message
	privé, vous pouvez désactiver cette option en vous rendant sur
	<a href="<?php echo $view['router']->url('zco_options_preferences') ?>">votre profil</a>.
</p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
