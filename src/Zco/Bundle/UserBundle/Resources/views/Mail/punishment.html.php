<p>Bonjour <strong><?php echo htmlspecialchars($pseudo) ?></strong> !</p>

<p>
	Nous vous informons qu'un administrateur
	(<a href="<?php echo $view['router']->url('zco_user_profile', array('id' => $adminId, 'slug' => rewrite($adminPseudo))) ?>"><?php echo htmlspecialchars($adminPseudo) ?></a>)
	vous a sanctionné sur le site des zCorrecteurs.
</p>

<p><strong>Raison donnée par l'administrateur :</strong><br />
<?php echo nl2br(htmlspecialchars($reason)) ?></p>

<p>Cordialement,<br />
<em>L'équipe des zCorrecteurs.</em></p>
