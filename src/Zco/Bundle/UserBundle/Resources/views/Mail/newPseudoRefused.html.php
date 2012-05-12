<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous aviez demandé un changement de pseudo (pour <em><?php echo htmlspecialchars($newPseudo) ?></em>)
	sur notre site. Celui-ci a été refusé par un administrateur
	(<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $adminId, 'slug' => rewrite($adminPseudo)), true) ?>"><?php echo htmlspecialchars($adminPseudo) ?></a>).
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($reason)) ?></p>

<ul>
	<li><a href="<?php echo $view['router']->generate('zco_about_contact', array(), true) ?>">Contacter les administrateurs</a></li>
	<li><a href="<?php echo URL_SITE ?>/options/">Mes options</a></li>
</ul>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
