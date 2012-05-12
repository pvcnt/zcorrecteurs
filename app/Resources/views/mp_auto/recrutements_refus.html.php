Bonjour <gras><?php echo htmlspecialchars($pseudo) ?></gras> !

Vous avez postulé lors du recrutement « <lien url="<?php echo URL_SITE ?>/recrutement/recrutement-<?php echo $id ?>.html"><?php echo $nom ?></lien> ». Votre candidature a été refusée.
Nous vous remercions toutefois sincèrement de nous avoir proposé votre aide. Si vous le souhaitez, vous pourrez vous présenter de nouveau au prochain recrutement en tenant compte des remarques qui vous ont été formulées.

<citation nom="Raison donnée par l'équipe"><?php echo $raison ?></citation>

<liste>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/">Accéder à l'espace recrutement</lien></puce>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/postuler-<?php echo $id ?>.html">Voir votre candidature</lien></puce>
</liste>

Cordialement,
<italique>L'équipe des zCorrecteurs.</italique>
