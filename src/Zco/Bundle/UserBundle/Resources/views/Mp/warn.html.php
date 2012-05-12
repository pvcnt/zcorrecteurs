Bonjour,

<lien url="<?php echo $view['router']->generate('zco_user_profile', array('id' => $id, 'slug' => rewrite($pseudo))) ?>"><?php echo htmlspecialchars($pseudo) ?></lien> vient d'ajouter une note à votre profil. Votre niveau d'avertissement <?php echo $action ?>.

Voici la raison donnée :
<citation nom="<?php echo htmlspecialchars($pseudo) ?>"><?php echo $reason ?></citation>

Cordialement,
<italique>L'équipe des zCorrecteurs.</italique>