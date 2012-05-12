<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez demandé un changement d'adresse mail. Merci de cliquer sur le lien
	suivant pour confirmer l'utilisation de cette adresse mail
	(<strong><?php echo htmlspecialchars($nouvelle_adresse) ?></strong>) à la
	place de l'ancienne (<?php echo htmlspecialchars($ancienne_adresse) ?>).</p>

<p>
	<a href="<?php echo URL_SITE ?>/options/modifier-mail.html?hash=<?php echo $hash ?>">
		<?php echo URL_SITE ?>/options/modifier-mail.html?hash=<?php echo $hash ?>
	</a>
</p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
