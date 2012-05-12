<div class="UI_box gras centre">
	<?php if(!empty($CandidaturePrecedente)){ ?>
	<a href="candidature-<?php echo $CandidaturePrecedente; ?>.html">Candidature précédente</a> -
	<?php } ?>
	<a href="recrutement-<?php echo $IdRecrutement; ?>.html#candidatures">
		Liste des candidatures
	</a>
	<?php if(!empty($CandidatureSuivante)){ ?> -
	<a href="candidature-<?php echo $CandidatureSuivante; ?>.html">Candidature suivante</a>
	<?php } ?>
</div>