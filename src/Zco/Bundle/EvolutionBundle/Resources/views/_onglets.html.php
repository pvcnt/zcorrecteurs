<div class="UI_tabs">
    <div class="tab<?php if($app->getRequest()->attributes->get('_action') == 'index') echo ' selected'; ?>">
    	<a href="index.html">Vue globale</a>
    </div>
    <div class="tab<?php if(in_array($app->getRequest()->attributes->get('_action'), array('demandes', 'demande', 'repondre', 'rechercher_anomalie')) && (empty($type) || $type == 'bug')) echo ' selected'; ?>">
    	<a href="demandes-1.html">Liste des anomalies</a>
    </div>
    <div class="tab<?php if(in_array($app->getRequest()->attributes->get('_action'), array('demandes', 'demande', 'repondre', 'rechercher_anomalie')) && (!empty($type) && $type == 'tache')) echo ' selected'; ?>">
    	<a href="demandes-2.html">Liste des tâches</a>
    </div>
    <?php if(verifier('tracker_etre_assigne')){ ?>
    <div class="tab<?php if($app->getRequest()->attributes->get('_action') == 'todo') echo ' selected'; ?>">
    	<a href="todo.html">Demandes assignées</a>
    </div>
    <?php } ?>
</div>

<div class="UI_tab_cleaner"></div>
