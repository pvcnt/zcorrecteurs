<ul class="nav nav-tabs">
	<li<?php if (!isset($currentTab) || $currentTab === 'campaigns') echo ' class="active"' ?>>
		<a href="gestion.html?etat[]=en_cours&etat[]=pause&etat[]=termine<?php if (verifier('publicite_voir')) echo '&all=1' ?>">
			Campagnes
		</a>
	</li>
	<li<?php if (isset($currentTab) && $currentTab === 'new') echo ' class="active"'; ?>>
		<a href="ajouter<?php if (isset($campagne_id)) echo '-'.$campagne_id ?>.html">
			Créer une publicité
		</a>
	</li>
</ul>