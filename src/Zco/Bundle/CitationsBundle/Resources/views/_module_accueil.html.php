<?php
if($CitationsExistantes) {
foreach($listageCitations as $cle => $item) { ?>
<div class="citation">
	<p><?php echo nl2br(htmlspecialchars($item['citation_contenu'])); ?>

	<br style="clear: both;" />
	<span><em>
		<?php echo htmlspecialchars($item['citation_auteur_prenom']); ?>
		<?php echo htmlspecialchars($item['citation_auteur_nom']); ?>
		<?php echo htmlspecialchars($item['citation_auteur_autres']); ?></em></span>
	</p>
</div>
<?php
}
} else
	echo '<p>Aucune citation à afficher.</p>';
?>
