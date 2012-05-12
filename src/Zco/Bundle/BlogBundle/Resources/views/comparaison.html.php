<?php $view->extend('::layouts/default.html.php') ?>

<h1>Comparaison de deux versions</h1>

<?php $diff = false; if($infos_old['version_titre'] != $infos_new['version_titre']){ $diff = true; ?>
<p>
	<strong>Le titre a été modifié :</strong>
	<?php htmlspecialchars($infos_old['version_titre']); ?> &rarr;
	<?php echo htmlspecialchars($infos_new['version_titre']); ?>.
</p>
<?php } ?>

<?php if($infos_old['version_sous_titre'] != $infos_new['version_sous_titre']){ $diff = true; ?>
<p>
	<strong>Le sous-titre a été modifié :</strong>
	<?php echo !empty($infos_old['version_sous_titre']) ? htmlspecialchars($infos_old['version_sous_titre']) : '(aucun)'; ?> &rarr;
	<?php echo !empty($infos_new['version_sous_titre']) ? htmlspecialchars($infos_new['version_sous_titre']) : '(aucun)'; ?>
</p>
<?php } ?>

<?php if(!empty($diff_intro)){ $diff = true; ?>
<p><strong>L'introduction a été modifiée :</strong></p>
<?php echo $diff_intro; ?>
<?php } ?>

<?php if(!empty($diff_texte)){ $diff = true; ?>
<p><strong>Le texte a été modifié :</strong></p>
<?php echo $diff_texte; ?>
<?php } ?>

<?php if(!$diff){ ?>
<p>Aucune différence n'a été trouvée : les deux versions sont identiques.</p>
<?php } ?>