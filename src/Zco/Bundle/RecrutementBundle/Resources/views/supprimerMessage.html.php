<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer un commentaire</h1>

<form method="post" action="">
<fieldset><legend>Suppression d'un commentaire</legend>
<p>Si vous validez cette action, le commentaire sera supprimé définitivement. Êtes-vous sûr de vouloir poursuivre ?</p>
<div class="send">
	<input type="submit" name="submit" value="Oui" /> <input type="submit" name="cancel" value="Non" />
</div>
</fieldset>
</form>
