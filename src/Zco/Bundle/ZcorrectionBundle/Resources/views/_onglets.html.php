<div class="UI_tabs">
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') === 'fiche_tuto') echo ' selected' ?>">
		<a href="fiche-tuto-<?php echo $s['soumission_id'] ?>.html">Fiche du tutoriel</a>
	</div>
	<?php if(verifier('voir_tutos_attente') || verifier('voir_tutos_correction')){ ?>
	<div class="tab<?php if (substr($app->getRequest()->attributes->get('_action') , 0, 4) === 'voir') echo ' selected' ?>">
		<a href="voir-<?php echo $s['soumission_type_tuto'] == BIG_TUTO ? 'big' : 'mini' ?>-tuto-<?php echo $s['id_tuto_recorrection'] ?: ($s['id_tuto_correction'] ?: $s['soumission_id_tuto']); ?>.html?cid=<?php echo $s['soumission_id'] ?>">
			Voir le tutoriel
		</a>
	</div>
	<?php } ?>
	<?php if (($s['soumission_recorrection'] && !empty($s['recorrection_date_debut']) && empty($s['recorrection_date_fin'])) || (!$s['soumission_recorrection'] AND !empty($s['correction_date_debut']) && empty($s['correction_date_fin']))){ ?>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') === 'corriger') echo ' selected' ?>">
		<a href="corriger-<?php echo $s['soumission_id'] ?>.html">Modifier le tutoriel</a>
	</div>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') === 'importer') echo ' selected' ?>">
		<a href="importer-<?php echo $s['soumission_id'] ?>.html">Importer ma correction</a>
	</div>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') === 'commentaire') echo ' selected' ?>">
		<a href="commentaires-<?php echo $s['soumission_id'] ?>.html?cid">Modifier les commentaires</a>
	</div>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') === 'terminer') echo ' selected' ?>">
		<a href="terminer-<?php echo $s['soumission_id'] ?>.html">Terminer la correction</a>
	</div>
	<?php } else{ ?>
	<div class="tab inactive">Modifier le tutoriel</div>
	<div class="tab inactive">Importer ma correction</div>
	<div class="tab inactive">Modifier les commentaires</div>
	<div class="tab inactive">Terminer la correction</div>
	<?php } ?>
</div>
