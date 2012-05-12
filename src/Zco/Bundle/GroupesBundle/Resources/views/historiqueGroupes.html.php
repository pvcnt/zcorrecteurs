<?php $view->extend('::layouts/default.html.php') ?>

<h1>Historique des changements de groupe</h1>

<p class="gras centre">
	Il y a eu <?php echo $NombreDeChangements; ?> changement<?php echo pluriel($NombreDeChangements); ?>
	de groupe.
</p>

<table class="UI_items">
	<thead>
		<tr>
			<td colspan="5">
				Page : <?php foreach($TableauPage as $element) echo $element ?>
			</td>
		</tr>
		<tr class="header_message">
			<th>Membre concerné</th>
			<th>Responsable</th>
			<th>Date</th>
			<th>Ancien groupe</th>
			<th>Nouveau groupe</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				Page : <?php foreach($TableauPage as $element) echo $element ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
		<?php if(!empty($Changements)) { foreach($Changements as $valeur) { ?>
			<tr>
				<td class="centre"><?php if(!empty($valeur['id_membre'])) { ?><a href="/membres/profil-<?php echo $valeur['id_membre'];?>-<?php echo rewrite($valeur['pseudo_membre']);?>.html"><?php echo htmlspecialchars($valeur['pseudo_membre']);?></a><?php } else { echo htmlspecialchars($valeur['pseudo_membre']); } ?></td>
				<td class="centre"><?php if(!empty($valeur['id_responsable'])) { ?><a href="/membres/profil-<?php echo $valeur['id_responsable']; ?>-<?php echo rewrite($valeur['pseudo_responsable']); ?>.html"><?php echo htmlspecialchars($valeur['pseudo_responsable']); ?></a><?php } else { echo htmlspecialchars($valeur['pseudo_responsable']); } ?></td>
				<td class="centre"><?php echo dateformat($valeur['chg_date']); ?></td>
				<td class="centre"><span style="color:<?php echo $valeur['couleur_ancien_groupe']; ?>"><?php echo $valeur['nom_ancien_groupe'];?></span></td>
				<td class="centre"><span style="color:<?php echo $valeur['couleur_nouveau_groupe']; ?>"><?php echo $valeur['nom_nouveau_groupe']; ?></span></td>
			</tr>
		<?php } } else { ?>
			<tr>
				<td colspan="6" class="centre">Aucun changement de groupe dans l'historique.</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

