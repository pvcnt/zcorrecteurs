<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Vous aviez demandé un changement de pseudo (pour <em><?php echo htmlspecialchars($newPseudo) ?></em>)
	sur notre site. Celui-ci a été refusé par un administrateur
	(<a href="<?php echo $view['router']->url('zco_user_profile', array('id' => $adminId, 'slug' => rewrite($adminPseudo))) ?>"><?php echo htmlspecialchars($adminPseudo) ?></a>).
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($reason)) ?></p>

<ul>
	<li><a href="<?php echo $view['router']->url('zco_about_contact') ?>">Contacter les administrateurs</a></li>
	<li><a href="<?php echo $view['router']->url('zco_options_index') ?>">Mes options</a></li>
</ul>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
