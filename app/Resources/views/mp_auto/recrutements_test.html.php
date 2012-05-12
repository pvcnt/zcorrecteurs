Bonjour <gras><?php echo htmlspecialchars($pseudo) ?></gras> !

<position valeur="centre"><couleur nom="vertf"><gras>Votre candidature lors du recrutement « <lien url="<?php echo URL_SITE ?>/recrutement/recrutement-<?php echo $id ?>.html"><?php echo $nom ?></lien> » a retenu toute notre attention, et nous vous estimons apte à passer la deuxième épreuve.</gras></couleur></position>

<?php echo $explicatif ?>

<attention>La date limite pour réaliser l'épreuve est <gras><couleur nom="rouge"><?php echo dateformat($date, MINUSCULE) ?></couleur></gras>.
Passé cette échéance, votre candidature ne pourra plus être considérée comme recevable.</attention>

Bonne chance ! ;)

<liste>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/">Accéder à l'espace recrutement</lien></puce>
	<puce><lien url="<?php echo URL_SITE ?>/recrutement/postuler-<?php echo $id ?>.html">Voir votre candidature et corriger le test</lien></puce>
</liste>

Cordialement,
<italique>L'équipe des zCorrecteurs.</italique>
