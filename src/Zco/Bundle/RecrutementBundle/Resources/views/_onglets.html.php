<ul class="nav nav-tabs">
	<li<?php if ($app->getRequest()->attributes->get('_action') === 'index') echo ' class="active"' ?>>
		<a href="index.html">Accueil</a>
	</li>
	<li<?php if ($app->getRequest()->attributes->get('_action') === 'pourquoi_nous_rejoindre') echo ' class="active"' ?>>
		<a href="pourquoi-nous-rejoindre.html">Pourquoi nous rejoindre ?</a>
	</li>
	<li<?php if (in_array($app->getRequest()->attributes->get('_action'), array('liste', 'recrutement', 'postuler', 'candidature'))) echo ' class="active"' ?>>
		<a href="liste.html">Bénévoles recherchés</a>
	</li>
</ul>
