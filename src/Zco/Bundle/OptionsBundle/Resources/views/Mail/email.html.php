<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous avez demandé un changement d'adresse mail. Merci de cliquer sur le lien
	suivant pour confirmer l'utilisation de cette adresse mail
	(<strong><?php echo htmlspecialchars($newEmail) ?></strong>) à la
	place de l'ancienne (<?php echo htmlspecialchars($oldEmail) ?>).</p>

<p>
	<a href="<?php echo $view['router']->generate('zco_options_validateEmail', array('hash' => $hash), true) ?>">
		<?php echo $view['router']->generate('zco_options_validateEmail', array('hash' => $hash), true) ?>
	</a>
</p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
