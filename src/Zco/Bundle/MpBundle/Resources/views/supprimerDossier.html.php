<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un dossier</h1>
<fieldset>
<legend>Confirmation de la suppression d'un dossier</legend>
<p>Confirmez-vous la suppression du dossier <strong><?php echo htmlspecialchars($DossierExiste['mp_dossier_titre']); ?></strong> ?<br />
Les MP éventuellement contenus dans ce dossier seront déplacés dans le dossier <strong>Accueil</strong>.</p>
<form action="" method="post">
<input type="submit" name="confirmation" value="Oui" />
<input type="submit" name="annuler" value="Non" />
</form>
</fieldset>
