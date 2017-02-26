<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous aviez demandé un changement de pseudo sur notre site. Celui-ci a été
	accepté par un administrateur
	(<a href="<?php echo $view['router']->url('zco_user_profile', array('id' => $adminId, 'slug' => rewrite($adminPseudo))) ?>"><?php echo htmlspecialchars($adminPseudo) ?></a>).<br />
	Votre nouveau pseudo pour la connexion est donc <strong><?php echo htmlspecialchars($newPseudo) ?></strong>.
	La connexion automatique, si activée, ne marchera pas la première fois
	suite à ce changement.
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($reason)) ?></p>

<ul>
	<li><a href="<?php echo $view['router']->url('zco_about_contact') ?>">Contacter les administrateurs</a></li>
	<li><a href="<?php echo $view['router']->url('zco_options_index') ?>">Mes options</a></li>
</ul>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
