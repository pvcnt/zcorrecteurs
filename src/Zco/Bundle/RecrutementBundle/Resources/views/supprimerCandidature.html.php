<?php $view->extend('::layouts/default.html.php') ?>

<h1>Supprimer une candidature</h1>

<fieldset>
	<legend>Supprimer une candidature</legend>
	<form method="post" action="">
		<p class="centre">
			Êtes-vous sûr de vouloir supprimer cette candidature de <strong><a href="/membres/profil-<?php echo $InfosCandidature['utilisateur_id']; ?>-<?php echo rewrite($InfosCandidature['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosCandidature['utilisateur_pseudo']); ?></a></strong>
			dans le recrutement <strong><?php echo htmlspecialchars($InfosCandidature['recrutement_nom']); ?></strong> ?
		</p>

		<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_ENVOYE, CANDIDATURE_REDACTION, CANDIDATURE_TESTE))){ ?>
		<p class="centre rouge gras">
			L'état de ce candidat n'est pas fixé, vous devriez d'abord attendre ses envois ou lui répondre.
		</p>
		<?php } ?>

		<p class="centre">
			<input type="submit" name="confirmer" value="Oui" /> <input type="submit" name="annuler" value="Non" />
		</p>
	</form>
</fieldset>
