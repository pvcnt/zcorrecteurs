<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Liste des annonces globales</h1>

<p>
	Les annonces constituent un puissant moyen de faire passer une information sur le site. 
	Elles prennent la forme d'un encart présent sur toutes les pages du site, juste 
	en-dessous de la bannière et au-dessus du fil d'Ariane. Une rotation de plusieurs annonces 
	est possible, en tenant compte des poids affectés à chacune.
</p>

<?php if (count($annonces) > 0 && (verifier('annonces_modifier') || verifier('annonces_publier'))): ?>
<form method="post" action="">
<div style="text-align: right;"><input type="submit" name="modifier" value="Appliquer les changements" /></div>
<?php endif; ?>

<?php if (count($annonces) > 0): ?>
<table class="UI_items">
	<thead>
		<tr>
			<th>Nom</th>
			<th style="width: 20%;">Date de début</th>
			<th style="width: 20%;">Date de fin</th>
			<th style="width: 10%;">Poids</th>
			<th style="width: 10%;">Active ?</th>
			<th style="width: 10%;">Actions</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach ($annonces as $annonce): ?>
		<tr<?php if ($annonce->estAffichable()) echo ' class="UI_inforow"' ?>>
			<td>
				<a onmouseover="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'block');" onmouseout="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'none');" title="Visualiser la bannière en action" href="/?_annonce=<?php echo $annonce['id'] ?>"><?php echo htmlspecialchars($annonce['nom']) ?></a>
				<div id="annonce<?php echo $annonce['id'] ?>" style="position: absolute; display: none; width: 80%; font-weight: normal;">
					<?php echo $annonce->genererHTML() ?>
				</div>
			</td>
			<td class="centre"><?php echo dateformat($annonce['date_debut']) ?></td>
			<td class="centre"><?php echo dateformat($annonce['date_fin']) ?></td>
			<td class="centre">
				<?php if (verifier('annonces_modifier')): ?>
					<select name="poids<?php echo $annonce['id'] ?>">
						<?php for ($i = 0 ; $i <= 100 ; $i += 5): ?>
						<option value="<?php echo $i ?>"<?php if ($annonce['poids'] == $i) echo ' selected="selected"' ?>>
							<?php echo $i ?>
						</option>
						<?php endfor; ?>
					</select>
				<?php else: ?>
					<?php echo $annonce['poids'] ?>
				<?php endif; ?>
			</td>
			<td class="centre">
				<?php if (verifier('annonces_publier')): ?>
					<input type="checkbox" name="actif<?php echo $annonce['id'] ?>"<?php if ($annonce['actif']) echo ' checked="checked"' ?> />
				<?php else: ?>
					<?php echo $annonce['actif'] ? 'Oui' : 'Non' ?>
				<?php endif; ?>
			</td>
			<td class="centre">
				<?php if (verifier('annonces_ajouter')): ?>
				<a href="ajouter-<?php echo $annonce['id'] ?>.html"><img src="/img/copier.png" alt="Copier" /></a>
				<?php endif; ?>
				<?php if (verifier('annonces_modifier')): ?>
				<a href="modifier-<?php echo $annonce['id'] ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
				<?php endif; ?>
				<?php if (verifier('annonces_supprimer')): ?>
				<a href="supprimer-<?php echo $annonce['id'] ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>Aucune annonce n'a encore été créée.</p>
<?php endif; ?>

<?php if (count($annonces) > 0 && (verifier('annonces_modifier') || verifier('annonces_publier'))): ?>
</form>
<?php endif; ?>
