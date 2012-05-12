<div class="UI_tabs">
	<div class="tab<?php if (in_array($app->getRequest()->attributes->get('_action'), array('index', 'modifier', 'supprimer'))) echo ' selected' ?>">
		<a href="index.html">Toutes les annonces</a>
	</div>
	<?php if (verifier('annonces_publier')): ?>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') == 'statistiques') echo ' selected' ?>">
		<a href="statistiques.html">Statistiques</a>
	</div>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') == 'allocation') echo ' selected' ?>">
		<a href="allocation.html">Allocation</a>
	</div>
	<?php endif; ?>

    <?php if (verifier('annonces_ajouter')): ?>
	<div class="tab<?php if ($app->getRequest()->attributes->get('_action') == 'ajouter') echo ' selected'; ?>" style="float: right;">
		<a href="ajouter.html">Créer une annonce</a>
	</div>
    <?php endif; ?>
</div>