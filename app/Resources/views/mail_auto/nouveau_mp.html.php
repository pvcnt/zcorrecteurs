<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez reçu un nouveau message privé sur le site des
	<a href="<?php echo URL_SITE ?>">zCorrecteurs</a>.<br />
	Il vous a été envoyé par <a href="<?php echo URL_SITE ?>/membres/profil-<?php echo $auteur_id ?>.html"><?php echo htmlspecialchars($auteur_pseudo) ?></a>
	et son titre est <?php echo htmlspecialchars($titre) ?>.</p>

<p>
	<a href="<?php echo URL_SITE ?>/mp/lire-<?php echo $id ?>.html">Cliquez ici</a> pour le lire.<br />
	<a href="<?php echo URL_SITE ?>/mp/">Accueil de la messagerie privée</a>
</p>

<p>
	Si vous ne souhaitez plus recevoir d'email quand vous recevez un message
	privé, vous pouvez désactiver cette option en vous rendant sur
	<a href="<?php echo URL_SITE ?>/options/navigation.html">votre profil</a>.
</p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
