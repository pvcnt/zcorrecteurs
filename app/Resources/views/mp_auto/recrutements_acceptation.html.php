Bonjour <gras><?php echo htmlspecialchars($pseudo) ?></gras> !

Vous vous étiez proposé lors du recrutement <gras><lien url="<?php echo URL_SITE ?>/recrutement/recrutement-<?php echo $id ?>.html"><?php echo $nom ?></lien></gras>. Votre candidature a été acceptée.
Vous disposez dès à présent des droits nécessaires. Nous vous prions de commencer par vous rendre dans les forums privés où un sujet d'accueil vient d'être ouvert à votre attention. Bienvenue dans l'équipe !

<citation nom="Raison donnée par l'équipe"><?php echo $raison ?></citation>

<liste>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/">Accéder à l'espace recrutement</lien></puce>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/postuler-<?php echo $id ?>.html">Voir votre candidature</lien></puce>
</liste>

Cordialement,
<italique>L'équipe des zCorrecteurs.</italique>
