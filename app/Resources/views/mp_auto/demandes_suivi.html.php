Bonjour <gras><?php echo htmlspecialchars($pseudo) ?></gras> !

Une modification a été apportée par <?php echo htmlspecialchars($posteur) ?> à une de vos demandes suivies, <gras><lien url="/evolution/<?php echo $url ?>"><?php echo htmlspecialchars($nom) ?></lien></gras>.

<?php if (!empty($texte)): ?>Voici ce qu'il en dit :
<citation nom="<?php echo $posteur ?>"><?php echo $texte ?></citation><?php endif; ?>

<liste>
	<puce><lien url="/evolution/<?php echo $url ?>">Aller lire la demande</lien></puce>
	<puce><lien url="/evolution/">Accueil du module d'évolution du site</lien></puce>
</liste>